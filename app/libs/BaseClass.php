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
    public $config = [];
    public $className = 'UNKNOWN';

    public function __construct($config = null)
    {
        $this->className = $this->getClassName();
        if(is_array($config))
        {
            $this->config = $config;
        }
    }

    // Get Class Name as a string
    public function getClassName()
    {
        return static::class;
    }
}