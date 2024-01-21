<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the custom `favicon.ico` Model.
 *
 ***************************************************************************************************
 */

 class FaviconIcoModel extends Model {
    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        // Set defaults
        $defaultFaviconIco = "$this->basedir/app/default/favicon.ico";
        $customFaviconIco  = "/etc/lorg/template/".$config['template']."/favicon.ico";

        $filedata = '';
        $filesize = 0;

        if (file_exists($customFaviconIco)) {
            $filedata = file_get_contents($customFaviconIco);
            $filesize = filesize($customFaviconIco);
        } elseif (file_exists($defaultFaviconIco)) {
            $filedata = file_get_contents($defaultFaviconIco);
            $filesize = filesize($defaultFaviconIco);
        }

        $this->data = [
            'data' => $filedata,
            'size' => $filesize,
            'type' => 'image/x-icon'
        ];
    }
 }