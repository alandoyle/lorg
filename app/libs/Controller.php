<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Base Controller
 * Loads the Models and views
 *
 ***************************************************************************************************
 */

class Controller extends BaseClass {
    protected $modelName = 'InternalError';
    protected $viewName = 'InternalError';

    public function __construct()
    {
        parent::__construct();

        $this->modelName = str_replace('Controller', '', $this->className);
        $this->viewName  = str_replace('Controller', '', $this->className);

        $this->load_config();
    }

    //Load Model
    public function loadModel($model)
    {
        // check for model file
        if(file_exists("../app/models/$model.php")) {
            require_once "../app/models/$model.php";
        } else {
            http_response_code(501);
            die("Model ($model) does not exist!");
        }

        $modelClassName = $model."Model";
        
        //Instantiate Model
        return new $modelClassName($this->config);
    }

    // load views
    public function loadView($view, $model)
    {
        # check for view file
        if(file_exists("../app/views/$view.php")) {
            require_once "../app/views/$view.php";
        } else {
            http_response_code(501);
            die("View ($view) does not exist!");
        }

        $viewClassName = $view."View";

         //Instantiate View
        return new $viewClassName($model);
    }

    function redirect_to_url($url, $response_code = 302)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header("Location: $url", true, $response_code);
        die();
    }

    function load_config()
    {
        $defaults = [
			'google_domain'            => 'com',
			'google_language_site'     => 'en',
			'google_language_results'  => 'en',
			'google_number_of_results' => 20,
			'opensearch_title'         => 'lorg',
			'opensearch_description'   => 'lorg is a metasearch engine that respects your privacy.',
			'opensearch_encoding'      => 'UTF-8',
			'opensearch_long_name'     => 'lorg Metasearch Engine',
			'use_image_proxy'          => true,
			'wikipedia_language'       => 'en',
			'use_qwant_for_images'     => false,
			'use_invidious_for_videos' => false,
			'invidious_url'            => 'https://y.com.sb',
        ];

        // Guesstimate the base URL
        $protocol = (!empty($_SERVER['HTTP_X_FORWARDED_SCHEME'])) ? $_SERVER['HTTP_X_FORWARDED_SCHEME'] : $_SERVER['REQUEST_SCHEME'];
        $defaults['base_url'] = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $pos = strrpos($defaults['base_url'], "/");
        if ($pos !== false) {
            // found...
            $defaults['base_url'] = substr($defaults['base_url'], 0, $pos);
        }

        // Load any existing configuration file
        if (file_exists('../config/config.php')) {
            $this->config = require '../config/config.php';
        } else {
            $this->config = [];
        }

        // Set defaults for missing entries
        $this->config['base_url']                 = array_key_exists('base_url',                 $this->config) ? $this->config['base_url']                 : $defaults['base_url'];
        $this->config['google_domain']            = array_key_exists('google_domain',            $this->config) ? $this->config['google_domain']            : $defaults['google_domain'];
        $this->config['google_language_site']     = array_key_exists('google_language_site',     $this->config) ? $this->config['google_language_site']     : $defaults['google_language_site'];
        $this->config['google_language_results']  = array_key_exists('google_language_results',  $this->config) ? $this->config['google_language_results']  : $defaults['google_language_results'];
        $this->config['google_number_of_results'] = array_key_exists('google_number_of_results', $this->config) ? $this->config['google_number_of_results'] : $defaults['google_number_of_results'];
        $this->config['opensearch_title']         = array_key_exists('opensearch_title',         $this->config) ? $this->config['opensearch_title']         : $defaults['opensearch_title'];
        $this->config['opensearch_description']   = array_key_exists('opensearch_description',   $this->config) ? $this->config['opensearch_description']   : $defaults['opensearch_description'];
        $this->config['opensearch_encoding']      = array_key_exists('opensearch_encoding',      $this->config) ? $this->config['opensearch_encoding']      : $defaults['opensearch_encoding'];
        $this->config['opensearch_long_name']     = array_key_exists('opensearch_long_name',     $this->config) ? $this->config['opensearch_long_name']     : $defaults['opensearch_long_name'];
        $this->config['use_image_proxy']          = array_key_exists('use_image_proxy',          $this->config) ? $this->config['use_image_proxy']          : $defaults['use_image_proxy'];
        $this->config['wikipedia_language']       = array_key_exists('wikipedia_language',       $this->config) ? $this->config['wikipedia_language']       : $defaults['wikipedia_language'];
        $this->config['use_qwant_for_images']     = array_key_exists('use_qwant_for_images',     $this->config) ? $this->config['use_qwant_for_images']     : $defaults['use_qwant_for_images'];
        $this->config['use_invidious_for_videos'] = array_key_exists('use_invidious_for_videos', $this->config) ? $this->config['use_invidious_for_videos'] : $defaults['use_invidious_for_videos'];
        $this->config['invidious_url']            = array_key_exists('invidious_url',            $this->config) ? $this->config['invidious_url']            : $defaults['invidious_url'];
    }
}