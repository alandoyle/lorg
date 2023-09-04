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
 **************************************************************************************************/

// Load Constants
require_once 'libs/Constants.php';

// Load up helper functions
require_once 'libs/Helper.php';

//Loading Libraries
spl_autoload_register(function($className) {
    // Base Dir
    $basedir = dirname($_SERVER['DOCUMENT_ROOT']);
    // Find the class file
    if (file_exists("$basedir/app/libs/$className.php")) {
        require_once "libs/$className.php";
    } elseif (file_exists("$basedir/app/libs/engines/main/$className.php")) {
        require_once "libs/engines/main/$className.php";
    } elseif (file_exists("$basedir/app/libs/engines/internal/$className.php")) {
        require_once "libs/engines/internal/$className.php";
    } else {
        debug_var("$basedir/app/libs/$className.php");
        debug_var("$basedir/app/libs/engines/main/$className.php");
        debug_var("$basedir/app/libs/engines/internal/$className.php");
        die("ERROR: Missing '$className' include file!");
    }
});