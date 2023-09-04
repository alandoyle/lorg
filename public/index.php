<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * Main Entrypoint.
 *
 **************************************************************************************************/

/***************************************************************************************************
 * Calculate the base directory of the install.
 **************************************************************************************************/
$basedir = dirname($_SERVER['DOCUMENT_ROOT']);

/***************************************************************************************************
 * Bootstrap the application
 **************************************************************************************************/
require_once "$basedir/app/bootstrap.php";

/***************************************************************************************************
 * Init Router Library
 **************************************************************************************************/
$engine = new Router($_SERVER['QUERY_STRING'], $basedir);