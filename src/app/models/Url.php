<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Url Model.
 */

class UrlModel extends Model {
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params = [])
    {
        $url = array_key_exists('url', $params) ? $params['url'] : false;
debug_array($params);
        if (empty($url)) {
            $error = "ERROR: No URL provided!";
            $this->data = [
                'url' => $this->config['base_url'], //@@@ Make a 404 handler???
            ];
        } else {
            $this->data['url']  = urldecode($url);
        }
    }
}