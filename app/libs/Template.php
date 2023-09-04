<?php
/**
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Template class based partially on the class located at https://codeshack.io/lightweight-template-engine-php/
 */

 class Template extends BaseClass {

	private $baseDir = '';

	private $template = 'lorg';

	private $minifyOutput = false;

	private $blocks = [];

    public function __construct($config)
    {
        parent::__construct($config);

		$this->baseDir      = $this->config['basedir'];
		$this->minifyOutput = $this->config['minify_output'];
		$this->template     = $this->config['template'];
    }

	public function render($file, $data = []) {
		$cached_data = $this->cache($file);
	    extract($data, EXTR_SKIP);
	   	eval($cached_data);
    }

	private function cache($file) {
		$code = $this->includeFiles($file);
		$code = $this->compileCode($code);
		if ($this->minifyOutput === true) {
			$code = str_replace('> <', '><', preg_replace("/(\s*[\r\n]+\s*|\s+)/", ' ', trim($code)));
		}
		return ' ?>'.$code.'<?php ';
	}

	private function compileCode($code) {
		$code = $this->compileBlock($code);
		$code = $this->compileYield($code);
		$code = $this->compileEscapedEchos($code);
		$code = $this->compileEchos($code);
		$code = $this->compilePHP($code);
		return $code;
	}

	private function includeFiles($file) {
		$code = file_get_contents($this->baseDir.'/template/'.$this->template.'/'.$file);
		preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			$code = str_replace($value[0], $this->includeFiles($value[2]), $code);
		}
		$code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
		return $code;
	}

	private function compilePHP($code) {
		return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $code);
	}

	private function compileEchos($code) {
		return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $1 ?>', $code);
	}

	private function compileEscapedEchos($code) {
		return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
	}

	private function compileBlock($code) {
		preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			if (!array_key_exists($value[1], $this->blocks)) {
				$this->blocks[$value[1]] = '';
			}
			if (strpos($value[2], '@parent') !== true) {
				$this->blocks[$value[1]] = $value[2];
			} else {
				$this->blocks[$value[1]] = str_replace('@parent', $this->blocks[$value[1]], $value[2]);
			}
			$code = str_replace($value[0], '', $code);
		}
		return $code;
	}

	private function compileYield($code) {
		foreach($this->blocks as $block => $value) {
			$code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
		}
		$code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
		return $code;
	}
}