<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Index Model.
 *
 ***************************************************************************************************
 */

 class IndexModel extends Model {

    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData()
    {
        $this->getBaseData();

        // Override the Search page logo (if available).
        if (file_exists("$this->basedir/custom/logo.svg")) {
            $this->data['sitelogo'] = 'site-logo-main';
        }
    }
}