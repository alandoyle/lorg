<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the custom `files` Model.
 *
 ***************************************************************************************************
 */

class CustomModel extends Model {
    public $filetype = 'text/plain';
    public $filedata = '';
    public $filesize = 0;

    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params)
    {
        $filename = array_key_exists('f', $params) ? $params['f'] : 'UNKNOWN';

        $customfile = '../custom/'.$filename;

        if (file_exists($customfile)) {
            $filetype = file_get_type($customfile);
            $filedata = file_get_contents($customfile);
            $filesize = filesize($customfile); 
        } else {
            // File doesn't exist so we send a 404
            http_response_code(404);
            $filedata = "ERROR: Unable to load data from '/custom/$filename'";
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