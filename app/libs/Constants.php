<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * PHP 7 Compatible Constants.
 * NOTE: Using ENUMS limits usage to PHP 8 or higher.
 *
 **************************************************************************************************/

/** Text Search */
define('SEARCH_TEXT',  0);
/** Image Search */
define('SEARCH_IMAGE', 1);
/** Video Search */
define('SEARCH_VIDEO', 2);
/** Video Search */
define('SEARCH_API',   3);

/** STRING Type Value */
define('VALUE_STRING',  0);
/** NUMERIC Type Value */
define('VALUE_NUMERIC', 1);
/** BOOLEAN Type Value */
define('VALUE_BOOLEAN', 2);

/** One Hour (seconds) */
define('ONE_HOUR',  3600);
/** Two Hours (seconds) */
define('TWO_HOURS', 7200);

/** JSON Minify Flag */
define('JSON_MINIFY_PRINT', 0);