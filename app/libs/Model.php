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

class Model extends BaseClass {
    protected $data = [];
    protected $basedir = '';

    public function __construct($config)
    {
        parent::__construct($config);
        $this->basedir = $config['basedir'];
    }

    public function getData()
    {
        return $this->data;
    }

    public function getBaseData($params = [])
    {
        $githash = '';
        $giturl = '';
        if (file_exists("$this->basedir/.git/refs/heads/main")) {
            $githash = trim(file_get_contents("$this->basedir/.git/refs/heads/main"));
            $giturl = "https://github.com/alandoyle/lorg/commit/$githash";
        }

        $this->data = [
            'githash'       => $githash,
            'giturl'        => $giturl,
            'baseurl'       => $this->config['base_url'],
            'apiurl'        => array_key_exists('api_url', $this->config) ? $this->config['api_url'] : '',
            'searchurl'     => '',
            'title'         => $this->config['opensearch_title'],
            'description'   => $this->config['opensearch_description'],
            'encoding'      => $this->config['opensearch_encoding'],
            'longname'      => $this->config['opensearch_long_name'],
            'template'      => $this->config['template'],
            'ua'            => $this->config['ua'],
            'contact_email' => $this->config['contact_email'],
            'result_count'  => 0,
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
        $this->data['special']    = [];
        $this->data['results']    = [];
    }
}