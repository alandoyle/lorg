<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 **************************************************************************************************/

/**
 * This is the Config class which handles the configuration of the application.
 *
 ***************************************************************************************************
 * Properties
 * ==========
 * @property-read string $basedir
 * @property-read array $defaults
 *
 ***************************************************************************************************
 * Public Methods
 * ==============
 * @method void __construct(string $querystring, string $basedir)
 *
 ***************************************************************************************************
 * Protected Methods
 * =================
 * @method void LoadConfig()
 *
 ***************************************************************************************************
 * Private Methods
 * ===============
 * @method string  getValue(string $key, int $type)
 * @method string getRandomApiUrl(array $instances)
 *
 **************************************************************************************************/
class Config extends BaseClass {
    protected $basedir = '';
    protected $defaults = [];

    public function __construct()
    {
        parent::__construct();

        /*******************************************************************************************
         * Set the defaults.
         ******************************************************************************************/
        $this->defaults = [
            'google_domain'            => 'com',
            'google_language_site'     => 'en',
            'google_language_results'  => 'en',
            'google_number_of_results' => 20,
            'opensearch_title'         => 'lorg',
            'opensearch_description'   => 'lorg is a metasearch engine that respects your privacy.',
            'opensearch_encoding'      => 'UTF-8',
            'opensearch_long_name'     => 'lorg Metasearch Engine',
            'template'                 => 'lorg',
            'link_google_image'        => false,
            'use_image_proxy'          => true,
            'minify_output'            => true,
            'include_local_instance'   => true,
            'wikipedia_language'       => 'en',
            'use_qwant_for_images'     => false,
            'use_invidious_for_videos' => false,
            'invidious_url'            => 'https://y.com.sb',
        ];

        /*******************************************************************************************
         * Load the Configuration.
         ******************************************************************************************/
        $this->LoadConfig();
        $this->basedir = $this->config['basedir'];
    }

    /**
     * Load in the application configuration.
     *
     * @param none
     * @return none
     */
    protected function LoadConfig()
    {
        $instances = [];
        $basedir   = dirname($_SERVER['DOCUMENT_ROOT']);

        /*******************************************************************************************
         * Guesstimate the base URL.
         ******************************************************************************************/
        $protocol = (!empty($_SERVER['HTTP_X_FORWARDED_SCHEME'])) ? $_SERVER['HTTP_X_FORWARDED_SCHEME'] : $_SERVER['REQUEST_SCHEME'];
        $this->defaults['base_url'] = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $pos = strrpos($this->defaults['base_url'], "/");
        if (is_numeric($pos)) {
            $this->defaults['base_url'] = substr($this->defaults['base_url'], 0, $pos);
        }

        /*******************************************************************************************
         * Load any existing configuration file.
         ******************************************************************************************/
        if (file_exists($basedir.'/config/config.php')) {
            $this->config = require $basedir.'/config/config.php';
        } else {
            $this->config = [];
        }

        /*******************************************************************************************
         * Set defaults for missing entries.
         ******************************************************************************************/
        $this->config['base_url']                 = $this->getValue('base_url',                 VALUE_STRING);
        $this->config['google_domain']            = $this->getValue('google_domain',            VALUE_STRING);
        $this->config['google_language_site']     = $this->getValue('google_language_site',     VALUE_STRING);
        $this->config['google_language_results']  = $this->getValue('google_language_results',  VALUE_STRING);
        $this->config['google_number_of_results'] = $this->getValue('google_number_of_results', VALUE_NUMERIC);
        $this->config['opensearch_title']         = $this->getValue('opensearch_title',         VALUE_STRING);
        $this->config['opensearch_description']   = $this->getValue('opensearch_description',   VALUE_STRING);
        $this->config['opensearch_encoding']      = $this->getValue('opensearch_encoding',      VALUE_STRING);
        $this->config['opensearch_long_name']     = $this->getValue('opensearch_long_name',     VALUE_STRING);
        $this->config['template']                 = $this->getValue('template',                 VALUE_STRING);
        $this->config['link_google_image']        = $this->getValue('link_google_image',        VALUE_BOOLEAN);
        $this->config['use_image_proxy']          = $this->getValue('use_image_proxy',          VALUE_BOOLEAN);
        $this->config['minify_output']            = $this->getValue('minify_output',            VALUE_BOOLEAN);
        $this->config['include_local_instance']   = $this->getValue('include_local_instance',   VALUE_BOOLEAN);
        $this->config['wikipedia_language']       = $this->getValue('wikipedia_language',       VALUE_STRING);
        $this->config['use_qwant_for_images']     = $this->getValue('use_qwant_for_images',     VALUE_BOOLEAN);
        $this->config['use_invidious_for_videos'] = $this->getValue('use_invidious_for_videos', VALUE_BOOLEAN);
        $this->config['invidious_url']            = $this->getValue('invidious_url',            VALUE_STRING);
        $this->config['ua']                       = get_ua();
        $this->config['result_count']             = 0;

        /*******************************************************************************************
         * This is the physical directory where the application and configurable files (template,
         * custom files, config, etc.) are located.
         *  e.g. /var/www/lorg
         ******************************************************************************************/
        $this->config['basedir'] = $basedir;

        /*******************************************************************************************
         * Load any existing instances file.
         ******************************************************************************************/
        if (file_exists($basedir.'/config/instances.json')) {
            $contents  = file_get_contents($basedir.'/config/instances.json');
            $instances = convert_to_array(json_decode($contents));
        }

        /*******************************************************************************************
         * Add local site to loaded instances
         ******************************************************************************************/
        if ($this->config['include_local_instance'] === true) {
            $instances['instances'][count($instances)] = [
                $this->config['opensearch_title'] => $this->config['base_url'].'/api'
            ];
        }

        $api_server = $this->getRandomApiUrl($instances['instances']);
        $this->config['api_url'] = '';
    }

    /**
     * Get a Value from the loaded config file, otherwise return the default.
     *
     * @param string $key
     * @param int $type
     * @return string
     */
    private function getValue($key, $type)
    {
        /*******************************************************************************************
         * Check if the default value exists.
         ******************************************************************************************/
        if (array_key_exists($key, $this->defaults) !== true) {
            switch ($type)
            {
                case VALUE_STRING:  return '';
                case VALUE_NUMERIC: return -1;
                case VALUE_BOOLEAN: return false;
            }
        }

        /*******************************************************************************************
         * If the '$key' doesn't exist then use the default value.
         ******************************************************************************************/
        $value = $this->defaults[$key];

        /*******************************************************************************************
         * If the '$key' doesn't exist then use the default value.
         ******************************************************************************************/
        if (array_key_exists($key, $this->config) !== true) {
            return $value;
        }

        /*******************************************************************************************
         * Validate the '$type' to ensure we don't try to use a sting as a number, etc.
         * If the contents are invalid then use the default value.
         ******************************************************************************************/
        switch ($type)
        {
            case VALUE_STRING:
                if (strlen($this->config[$key]) > 0) {
                    $value = $this->config[$key];
                }
                break;
            case VALUE_NUMERIC:
                if (is_numeric($this->config[$key]) === true) {
                    $value = $this->config[$key];
                }
                break;
            case VALUE_BOOLEAN:
                if (($this->config[$key] === true) ||
                    ($this->config[$key] === false)) {
                    $value = $this->config[$key];
                }
                break;
        }

        return $value;
    }

    /**
     * Return a random an array.
     *
     * @param array $instances
     * @return string
     */
    private function getRandomApiUrl($instances)
    {
        return '';
    }
}