<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the template `files` Model.
 *
 ***************************************************************************************************
 */

class TemplateModel extends Model {

    public $filetype = 'text/plain';

    public $filedata = '';

    public $filesize = 0;

    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params = [])
    {
        $filename     = array_key_exists('f', $params) ? $params['f'] : 'UNKNOWN';
        $template     = $this->config['template'];
        $templatefile = "$this->basedir/template/$template/$filename";

        if (file_exists($templatefile)) {
            $filetype = file_get_type($templatefile);
            $filedata = file_get_contents($templatefile);
            $filesize = filesize($templatefile);
        } else if ($filename == 'style.css') {
            $filetype = 'text/css';
            $filedata = '/* Auto-generated Template CSS */';
            $filesize = strlen($filedata);
        } else {
            // File doesn't exist so we send a 404
            http_response_code(404);
            $filedata = "ERROR: Unable to load data from '/template/$filename'";
            $filesize = strlen($filedata);
            $filetype = 'text/plain';
        }

        $this->data = [
            'data' => $filedata,
            'size' => $filesize,
            'type' => $filetype
        ];
    }
}