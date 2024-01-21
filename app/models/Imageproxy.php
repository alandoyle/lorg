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
    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        $url = urldecode(array_key_exists('url', $params) ? $params['url'] : '');

        if (empty($url)) {
            $error = "ERROR: No URL provided!";
            $this->data = [
                'data' => $error,
                'size' => strlen($error),
                'type' => 'text/plain',
            ];
        } else {
            $this->data  = download_url($url, $config['ua']);
        }
    }
}