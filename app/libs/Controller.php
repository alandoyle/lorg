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
 * Loads the Models and Views
 *
 **************************************************************************************************/

class Controller extends Config {
    protected $modelName = 'InternalError';
    protected $viewName = 'InternalError';

    public function __construct()
    {
        parent::__construct();

        $this->modelName =
        $this->viewName  = str_replace('Controller', '', $this->className);
    }

    // Load Model
    protected function LoadModel($model)
    {
        // check for model file
        if(file_exists("$this->basedir/app/models/$model.php")) {
            require_once "$this->basedir/app/models/$model.php";
        } else {
            http_response_code(501);
            die("Model ($model) does not exist!");
        }

        $modelClassName = $model."Model";

        //Instantiate Model
        return new $modelClassName($this->config);
    }

    // Load View
    protected function LoadView($view, $model)
    {
        # check for view file
        if(file_exists("$this->basedir/app/views/$view.php")) {
            require_once "$this->basedir/app/views/$view.php";
        } else {
            http_response_code(501);
            die("View ($view) does not exist!");
        }

        $viewClassName = $view."View";

         //Instantiate View
        return new $viewClassName($model);
    }

    protected function RedirectToURL($url, $response_code = 302)
    {
        if (!headers_sent()) {
            foreach (headers_list() as $header)
                header_remove($header);
        }

        header("Location: $url", true, $response_code);
        die();
    }
}