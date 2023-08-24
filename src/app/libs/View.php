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

class View extends Template {
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
        parent::__construct($this->model->config);
    }

    public function renderHtml($template, $data = [])
    {
        $this->renderTemplate('text/html', $template, $data);
    }

    public function renderJson($data = [])
    {
       $this->renderTemplate('application/json', '', $data);
    }

    public function renderXml($template, $data = [])
    {
       $this->renderTemplate('text/xml', $template, $data);
    }

    public function renderText($data)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header('Content-Type: text/plain; charset=utf-8');
        header("Content-Length: ".strlen($data['text']));
        echo $data['text'];
    }

    public function renderFileData($data)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header('Content-Type: '.$data['type']);
        header("Content-Length: ".$data['size']);
        echo $data['data'];
    }

    public function renderRedirect($data)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header("Location: ".$data['url']);
    }

    private function renderTemplate($type, $template, $data = [])
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header("Content-Type: $type");
        if (array_key_exists("data", $data)) {
            header("Content-Length: ".strlen($data['data']));
            echo $data['data'];
        } else {
            $this->render($template, $data);
        }
    }
}