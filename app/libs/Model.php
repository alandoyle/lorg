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

    public function readData($params = [])
    {
        $this->getBaseData($params);
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
            'githash'                  => $githash,
            'giturl'                   => $giturl,
            'baseurl'                  => $this->config['base_url'],
            'apiurl'                   => array_key_exists('api_url', $this->config) ? $this->config['api_url'] : '',
            'searchurl'                => '',
            'title'                    => $this->config['opensearch_title'],
            'description'              => $this->config['opensearch_description'],
            'encoding'                 => $this->config['opensearch_encoding'],
            'longname'                 => $this->config['opensearch_long_name'],
            'template'                 => $this->config['template'],
            'ua'                       => $this->config['ua'],
            'google_language_site'     => $this->config['google_language_site'],
            'google_language_results'  => $this->config['google_language_results'],
            'google_number_of_results' => $this->config['google_number_of_results'],
            'invidious_url'            => $this->config['invidious_url'],
            'api_disabled'             => $this->config['api_disabled'],
            'api_only_forced'          => $this->config['api_only_forced'],
            'hide_templates'           => $this->config['hide_templates'],
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
                    if (is_dir('/etc/lorg/template/$templatedir')) {
                        array_push($templates,
                            array (
                                "name"      => $templatedir,
                                "selected"  => ($templatedir == $this->config['template']) ? "selected" : "",
                            )
                        );
                    }
                    break;
            }
        }
        $this->data['templates'] = $templates;
    }
}