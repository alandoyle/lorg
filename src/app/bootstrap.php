<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * Bootstrap
 * 
 ***************************************************************************************************
 */

// Load up helper functions
require_once 'libs/Helper.php';

//Loading Libraries
spl_autoload_register(function($className) {
    if (file_exists("../app/libs/$className.php")) {
        require_once 'libs/'.$className.'.php';
    } elseif (file_exists('../app/libs/engines/main/'.$className.'.php')) {
        require_once 'libs/engines/main/'.$className.'.php';
    } elseif (file_exists('../app/libs/engines/internal/'.$className.'.php')) {
        require_once 'libs/engines/internal/'.$className.'.php';
    } else {
        die("ERROR: Missing '$className' include file!");
    }
});