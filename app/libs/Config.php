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
 * @method string getValue(string $key, int $type)
 * @method string getRandomApiServer(array $instances)
 * @method string getUserAgent()
 *
 **************************************************************************************************/
class Config extends BaseClass {
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
            'accept_langauge'          => 'en-US',
            'template'                 => 'lorg',
            'use_client_ua'            => false,
            'use_specific_ua'          => '',
            'link_google_image'        => false,
            'use_image_proxy'          => true,
            'minify_output'            => true,
            'include_local_instance'   => true,
            'wikipedia_language'       => 'en',
            'use_qwant_for_images'     => false,
            'use_invidious_for_videos' => false,
            'invidious_url'            => 'https://y.com.sb',
        ];
    }

    /**
     * Load in the application configuration.
     *
     * @param string $basedir
     * @return none
     */
    protected function LoadConfig($basedir)
    {
        $instances = [];

        /*******************************************************************************************
         * Calculate the BASEDIR if not given.
         ******************************************************************************************/
        if (empty($basedir)) {
            $basedir = dirname($_SERVER['DOCUMENT_ROOT']);
        }

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
        $this->config['accept_langauge']          = $this->getValue('accept_langauge',          VALUE_STRING);
        $this->config['template']                 = $this->getValue('template',                 VALUE_STRING);
        $this->config['use_client_ua']            = $this->getValue('use_client_ua',            VALUE_BOOLEAN);
        $this->config['use_specific_ua']          = $this->getValue('use_specific_ua',          VALUE_STRING);
        $this->config['link_google_image']        = $this->getValue('link_google_image',        VALUE_BOOLEAN);
        $this->config['use_image_proxy']          = $this->getValue('use_image_proxy',          VALUE_BOOLEAN);
        $this->config['minify_output']            = $this->getValue('minify_output',            VALUE_BOOLEAN);
        $this->config['include_local_instance']   = $this->getValue('include_local_instance',   VALUE_BOOLEAN);
        $this->config['wikipedia_language']       = $this->getValue('wikipedia_language',       VALUE_STRING);
        $this->config['use_qwant_for_images']     = $this->getValue('use_qwant_for_images',     VALUE_BOOLEAN);
        $this->config['use_invidious_for_videos'] = $this->getValue('use_invidious_for_videos', VALUE_BOOLEAN);
        $this->config['invidious_url']            = $this->getValue('invidious_url',            VALUE_STRING);
        $this->config['ua']                       = $this->getUserAgent();
        $this->config['result_count']             = 0;

        /*******************************************************************************************
         * Enabling 'link_google_image' makes no sense when using Qwant for images!
         ******************************************************************************************/
        if ($this->config['use_qwant_for_images'] === true) {
            $this->config['link_google_image'] = false;
        }

        /*******************************************************************************************
         * This is the physical directory where the application and configurable files (template,
         * custom files, config, etc.) are located.
         *  e.g. /var/www/lorg
         ******************************************************************************************/
        $this->basedir = 
        $this->config['basedir'] = $basedir;

        /*******************************************************************************************
         * Load any existing instances file.
         ******************************************************************************************/
        if (file_exists("$basedir/config/instances.json")) {
            $contents  = file_get_contents("$basedir/config/instances.json");
            $instances = json_decode($contents, true);
        }

        /*******************************************************************************************
         * Add local site to loaded instances
         ******************************************************************************************/
        if ($this->config['include_local_instance'] === true) {
            $instances['instances'][count($instances)] = [
                'Name' => 'LocalSite',
                'URL'  => $this->config['base_url'].'/api',
                'Type' => 'Local'
            ];
        }
        $this->getRandomApiServer($instances['instances']);
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
         * Set the '$key' to the default value.
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
     * Get a Value from a cookie, otherwise return the default.
     *
     * @param string $key
     * @param int $type
     * @return string
     */
    private function getCookieValue($key, $type)
    {
        /*******************************************************************************************
         * Obtain the default value.
         ******************************************************************************************/
        $value = $this->getValue($key, $type);

        /*******************************************************************************************
         * Check if the cookie is set?
         ******************************************************************************************/
        if (isset($_COOKIE[$key]) !== true) {
            return  $value;
        }

        /*******************************************************************************************
         * Validate the '$type' to ensure we don't try to use a sting as a number, etc.
         * If the contents are invalid then use the default value.
         ******************************************************************************************/
        switch ($type)
        {
            case VALUE_STRING:
                if (strlen($_COOKIE[$key]) > 0) {
                    $value = $_COOKIE[$key];
                }
                break;
            case VALUE_NUMERIC:
                if (is_numeric($_COOKIE[$key]) === true) {
                    $value = $_COOKIE[$key];
                }
                break;
            case VALUE_BOOLEAN:
                if (($_COOKIE[$key] == 'true') ||
                    ($_COOKIE[$key] == 'false')) {
                    $value = ($_COOKIE[$key] == 'true') ? true : false;
                }
                break;
        }

        return $value;
    }
    /**
     * Returns a UserAgent based on current configuration preferences.
     *
     * @param none
     * @return string
     */
    private function getUserAgent()
    {
        $enabled_by_cookie = $this->getCookieValue('use_client_ua', VALUE_BOOLEAN);
        $ua = '';

        if (empty(trim($this->config['use_specific_ua'])) === false) {
            $ua = $this->config['use_specific_ua'];
        } elseif ($this->config['use_client_ua'] === true || $enabled_by_cookie === true) {
            $ua = $_SERVER["HTTP_USER_AGENT"];
        } else {
            $ua = get_random_ua();
        }

        return $ua;
    }

    /**
     * Return a random API URL from an array.
     *
     * @param array $instances
     * @return none
     */
    private function getRandomApiServer($instances)
    {
        $count = 0;
        $found = false;

        do {
            $instance = $instances[array_rand($instances, 1)];
            $keyfile  =
            $contents = '';

            switch (strtolower($instance['Type']))
            {
                case 'local':
                    $keyfile = 'api.key';
                    break;
                case 'remote':
                    $keyfile = $instance['Name'].'.key';
                    break;
            }

            /*******************************************************************************************
             * Load the API Key.
             ******************************************************************************************/
            if (file_exists("$this->basedir/config/keys/$keyfile")) {
                $contents  = file_get_contents("$this->basedir/config/keys/$keyfile");
                if (strlen(trim($contents)) > 0) {
                    $this->config['api_url'] = $instance['URL'];
                    $this->config['api_key'] = trim($contents);
                    $found = true;
                }
            }

            /*******************************************************************************************
             * Remove duff entries from the instances list.
             ******************************************************************************************/
            if ($found === false) {
                for ($n = 0; $n < count($instances); $n++) {
                    if ($instances[$n]['Name'] === $instance['Name']) {
                        unset($instances[$n]);
                        $instances = array_values($instances);
                        break;
                    }
                }
            }
            $count++;
        }
        while(($found === false) && ($count < 5));
    }
}