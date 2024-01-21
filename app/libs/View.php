<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Base View
 *
 ***************************************************************************************************
 */

class View extends TemplateEngine {
    protected $model;
    protected $data = [];

    public function __construct($model)
    {
        $this->model = $model;
        parent::__construct($this->model->basedir);
    }

    public function renderView(&$config)
    {
		$this->minifyOutput = $config['minify_output'];
		$this->template     = $config['template'];

        $this->data = $this->model->getData();
    }

    public function renderHtml($template)
    {
        $this->renderTemplate('text/html', $template, $this->data);
    }

    public function renderJson()
    {
       $this->renderTemplate('application/json', '', $this->data);
    }

    public function renderXml($template)
    {
       $this->renderTemplate('text/xml', $template, $this->data);
    }

    public function renderText()
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                @header_remove($header);
        }

        @header('Content-Type: text/plain; charset=utf-8');
        @header("Content-Length: ".strlen($this->data['text']));
        echo $this->data['text'];
    }

    public function renderFileData()
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                @header_remove($header);
        }

        @header('Content-Type: '.$this->data['type']);
        @header("Content-Length: ".$this->data['size']);
        echo $this->data['data'];
    }

    public function renderRedirect($data)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                @header_remove($header);
        }

        @header("Location: ".$data['url']);
    }

    private function renderTemplate($type, $template, $data = [])
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                @header_remove($header);
        }
        @http_response_code(200);
        @header("Content-Type: $type");
        if (array_key_exists("data", $data)) {
            @header("Content-Length: ".strlen($data['data']));
            echo $data['data'];
        } else {
            $this->render($template, $data);
        }
    }
}