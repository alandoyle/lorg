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

    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        $this->getBaseData($config, $params);
    }

    public function getData()
    {
        return $this->data;
    }

    private function getBaseData(&$config, $params = [])
    {
        $githash = '';
        $giturl = '';
        if (file_exists("$this->basedir/.git/refs/heads/main")) {
            $githash = trim(file_get_contents("$this->basedir/.git/refs/heads/main"));
            $giturl = "https://github.com/alandoyle/lorg/commit/$githash";
        }

        $this->data = [
            'githash'                  => $githash,
            'giturl'                   => $giturl,
            'baseurl'                  => $config['base_url'],
            'apiurl'                   => '',
            'searchurl'                => '',
            'title'                    => $config['opensearch_title'],
            'description'              => $config['opensearch_description'],
            'encoding'                 => $config['opensearch_encoding'],
            'longname'                 => $config['opensearch_long_name'],
            'template'                 => $config['template'],
            'ua'                       => $config['ua'],
            'google_language_site'     => $config['google_language_site'],
            'google_language_results'  => $config['google_language_results'],
            'google_number_of_results' => $config['google_number_of_results'],
            'invidious_url'            => $config['invidious_url'],
            'api_enabled'              => $config['api_enabled'],
            'api_server_count'         => $config['api_server_count'],
            'hide_templates'           => $config['hide_templates'],
            'footer_message'           => $config['footer_message'],
            'result_count'             => 0,
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

        // Store URL encoded version of query
        $this->data['query_encoded'] = urlencode($this->data['query']);

        // Setup default
        $this->data['categories'] = [];
        $this->data['special']    = [];
        $this->data['results']    = [];

        // Add list of templates
        $templates = [];
        $templatedirs = scandir('/etc/lorg/template/');
        foreach($templatedirs as $templatedir) {
            switch ($templatedir)
            {
                case '.':
                case '..':
                    break;
                default:
                    if (is_dir("/etc/lorg/template/$templatedir")) {
                        array_push($templates,
                            array (
                                "name"      => $templatedir,
                                "selected"  => ($templatedir == $config['template']) ? "selected" : "",
                            )
                        );
                    }
                    break;
            }
        }
        $this->data['templates'] = $templates;
    }
}