<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the custom `robots.txt` Model.
 *
 ***************************************************************************************************
 */

 class RobotsTxtModel extends Model {
    protected $autoRobotTxt = "# Auto-generated robots.txt\nUser-agent: *\nDisallow: /";

    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        $defaultRobotTxt = "$this->basedir/app/default/robots.txt";
        $customRobotTxt  = "/etc/lorg/template/".$config['template']."/robots.txt";

        if (file_exists($customRobotTxt)) {
            $this->data['text'] = file_get_contents($customRobotTxt);
        } elseif (file_exists($defaultRobotTxt)) {
            $this->data['text'] = file_get_contents($defaultRobotTxt);
        } else {
            $this->data['text'] = $this->autoRobotTxt;
        }
    }
 }