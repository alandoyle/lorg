<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Base Model
 *
 ***************************************************************************************************
 */

class Model extends Template {
    protected $data = [];

    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getBaseData($params = [])
    {
        $githash = '';
        if (file_exists("../.git/refs/heads/main")) {
            $hash = trim(file_get_contents("../.git/refs/heads/main"));
            $githash = "<a href='https://github.com/alandoyle/lorg/commit/$hash' target='_blank'>Latest commit: $hash</a>";
        }

        $this->data = [
            'githash'     => $githash,
            'baseurl'     => $this->config['base_url'],
            'title'       => $this->config['opensearch_title'],
            'description' => $this->config['opensearch_description'],
            'encoding'    => $this->config['opensearch_encoding'],
            'longname'    => $this->config['opensearch_long_name'],
            'sitelogo'    => 'site-logo-search-default',
        ];

        // Set parameter defaults
        $this->data['query']   = '';
        $this->data['type']    = 0;
        $this->data['pagenum'] = 0;

        // Store the parameters
        $mappings = array('q' => 'query', 't' => 'type', 'p' => 'pagenum');
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $mappings)) {
            $this->data[$mappings[$key]] = $value;
            }
        }

        // Setup default
        $this->data['categories'] = [];
        $this->data['specials']   = [];
        $this->data['results']    = [];
    }
}