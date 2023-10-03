<?php
/**
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the TemplateEngine class based partially on the class located at https://codeshack.io/lightweight-template-engine-php/
 */

 class TemplateEngine extends BaseClass {
	protected $template = 'lorg';
	private $minifyOutput = false;
	private $blocks = [];

    public function __construct($config)
    {
        parent::__construct($config);

		$this->basedir      = $config['basedir'];
		$this->minifyOutput = $config['minify_output'];
		$this->template     = $config['template'];
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
		$fullpath = "$this->basedir/template/$this->template/$file";
		if (file_exists($fullpath)) {
			$code = file_get_contents($fullpath);
		}
		if ($file == 'base.tpl') {
			$code = $this->gen_base_template();
		}
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

	private function gen_base_template() {
		return
'<!DOCTYPE html >
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta charset="UTF-8"/>
		<meta name="description" content="{{ $description }}"/>
		<meta name="referrer" content="no-referrer"/>
		<meta name="copyright" content="(c) Alan Doyle [me@alandoyle.com]"/>
		<meta name="engine" content="lorg Metasearch Engine [https://github.com/alandoyle/lorg/]"/>
		<meta name="ua" content="{{ $ua }}"/>
		<meta name="search_url" content="{{ $searchurl }}"/>
		<meta name="base_url" content="{{ $baseurl }}"/>
{% if ($api_disabled != true): %}
		<meta name="api_url" content="{{ $apiurl }}"/>
{% endif; %}
		<meta name="template" content="{{ $template }}"/>
		<meta name="hide_templates" content="{{ $hide_templates }}"/>
		<meta name="result_count" content="{{ $result_count }}"/>
		<meta name="generated" content="'. date(DATE_RFC2822) .'"/>
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
		<meta http-equiv="Pragma" content="no-cache"/>
		<meta http-equiv="Expires" content="0"/>
{% if (!empty($githash)): %}
		<meta name="git-commit" content="{{ $githash }}"/>
		<meta name="git-url" content="{{ $giturl }}"/>
{% endif; %}
		<link title="{{ $title }}" type="application/opensearchdescription+xml" href="opensearch.xml?method=POST" rel="search"/>
		<link rel="stylesheet" type="text/css" href="css/base.css"/>
		<link rel="stylesheet" type="text/css" href="template/css/style.css"/>
		<title>{{ $title }}</title>
	</head>
	<body>
{% yield content %}
	</body>
</html>';
	}

}