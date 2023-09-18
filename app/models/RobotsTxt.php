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
    protected $defaultRobotTxt = '';
    protected $customRobotTxt = '';

    public function __construct($config)
    {
        parent::__construct($config);
        $this->defaultRobotTxt = "$this->basedir/app/default/robots.txt";
        $this->customRobotTxt  = "$this->basedir/template/".$config['template']."/robots.txt";
    }
    public function readData()
    {
        if (file_exists($this->customRobotTxt)) {
            $this->data['text'] = file_get_contents($this->customRobotTxt);
        } elseif (file_exists($this->defaultRobotTxt)) {
            $this->data['text'] = file_get_contents($this->defaultRobotTxt);
        } else {
            $this->data['text'] = $this->autoRobotTxt;
        }
    }
 }