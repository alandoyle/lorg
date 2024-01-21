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
    protected $basedir   = '';
    protected $className = 'UNKNOWN';

    public function __construct($basedir = '')
    {
        $this->className = $this->getClassName();
        $this->basedir   = $basedir;
    }

    public function RedirectToURL($url, $response_code = 302)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                @header_remove($header);
        }

        @header("Location: $url", true, $response_code);
        die();
    }

    public function SetBaseDir($basedir)
    {
        $this->basedir = $basedir;
    }

    // Get Class Name as a string
    private function getClassName()
    {
        return static::class;
    }
}