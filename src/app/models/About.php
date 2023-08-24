<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the About Model.
 */

 class AboutModel extends Model {
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params)
    {
        $this->getBaseData();
    }
}