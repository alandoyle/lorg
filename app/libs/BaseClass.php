<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Base class;
 *
 ***************************************************************************************************
 */

class BaseClass {
    protected $basedir = '';
    protected $config = [];
    protected $className = 'UNKNOWN';

    public function __construct($config = [])
    {
        $this->className = $this->getClassName();
        if (count($config) > 0) {
            $this->config = $config;
        }
    }

    public function execute($params)
    {
        $basedir = '';
        if (isset($params['_basedir_'])) {
            $basedir = $params['_basedir_'];
        }
        if(count($this->config) === 0) {
            $this->LoadConfig($basedir);
        }
        $this->basedir = $this->config['basedir'];
    }

    public function RedirectToURL($url, $response_code = 302)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header("Location: $url", true, $response_code);
        die();
    }

    // Get Class Name as a string
    private function getClassName()
    {
        return static::class;
    }
}