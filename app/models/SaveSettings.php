<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the SaveSettings Model.
 *
 ***************************************************************************************************
 */

 class SaveSettingsModel extends Model {

    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        parent::readData($params);

        foreach ($_REQUEST as $key => $value) {
            if ($key == 'm' || $key == 'r') {
                continue;
            }
            setcookie($key, $value);
        }
    }
}