<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Imageproxy Model.
 */

class ImageproxyModel extends Model {
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params = [])
    {
        $url = array_key_exists('url', $params) ? $params['url'] : false;

        if (empty($url)) {
            $error = "ERROR: No URL provided!";
            $this->data = [
                'data' => $error,
                'size' => strlen($error),
                'type' => 'text/plain',
            ];
        } else {
            $this->data  = download_url ($url);
        }
    }
}