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
    protected $defaultFaviconIco = '';
    protected $customFaviconIco = '';

    public function __construct($config)
    {
        parent::__construct($config);

        // Set defaults
        $this->defaultFaviconIco = "$this->basedir/app/default/favicon.ico";
        $this->customFaviconIco  = "$this->basedir/custom/favicon.ico";
    }

    public function readData($params = [])
    {
        $filedata = '';
        $filesize = 0;

        if (file_exists($this->customFaviconIco)) {
            $filedata = file_get_contents($this->customFaviconIco);
            $filesize = filesize($this->customFaviconIco);
        } elseif (file_exists($this->defaultFaviconIco)) {
            $filedata = file_get_contents($this->defaultFaviconIco);
            $filesize = filesize($this->defaultFaviconIco);
        }

        $this->data = [
            'data' => $filedata,
            'size' => $filesize,
            'type' => 'image/x-icon'
        ];
    }
 }