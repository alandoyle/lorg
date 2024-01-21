<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 **************************************************************************************************/

/**
 * This is the Base Controller class loads the Models and Views.
 *
 ***************************************************************************************************
 * Properties
 * ==========
 * @property-read string $modelName
 * @property-read string $viewName
 *
 ***************************************************************************************************
 * Public Methods
 * ==============
 * @method void __construct()
 * @method void execute($params)
 *
 ***************************************************************************************************
 * Private Methods
 * =================
 * @method Model LoadModel($model)
 * @method View  LoadView($view, $model)
 *
 **************************************************************************************************/
class Controller extends Config {
    protected $modelName = 'InternalError';
    protected $viewName  = 'InternalError';

    public function __construct()
    {
        parent::__construct();

        /*******************************************************************************************
         * Set the Model/View names
         ******************************************************************************************/
        $this->modelName =
        $this->viewName  = str_replace('Controller', '', $this->className);
    }

    public function execute($params)
    {
        /*******************************************************************************************
         * Store the basedir.
         ******************************************************************************************/
        $basedir = '';
        if (array_key_exists('basedir', $params)) {
            $basedir = $params['basedir'];
        }
        $this->SetBaseDir($basedir);

        /*******************************************************************************************
         * Load the Configuration
         ******************************************************************************************/
        if(count($this->config) === 0) {
            $this->LoadConfig($basedir);
        }
        $this->config['controller'] = $this->className;

        /*******************************************************************************************
         * Build the Model
         ******************************************************************************************/
        $model = $this->LoadModel($this->modelName);
        $model->readData($this->config, $params);

        /*******************************************************************************************
         * Render the View
         ******************************************************************************************/
        $view = $this->LoadView($this->viewName, $model);
        $view->renderView($this->config);
    }

    // Load Model
    private function LoadModel($model)
    {
        // check for model file
        if(file_exists("$this->basedir/app/models/$model.php")) {
            require_once "$this->basedir/app/models/$model.php";
        } else {
            http_response_code(501);
            die("Model ($model) does not exist!");
        }

        $modelClassName = $model."Model";
        $this->config['model'] = $modelClassName;

        //Instantiate Model
        return new $modelClassName($this->basedir);
    }

    // Load View
    private function LoadView($view, $model)
    {
        # check for view file
        if(file_exists("$this->basedir/app/views/$view.php")) {
            require_once "$this->basedir/app/views/$view.php";
        } else {
            http_response_code(501);
            die("View ($view) does not exist!");
        }

        $viewClassName = $view."View";
        $this->config['view'] = $viewClassName;

         //Instantiate View
        return new $viewClassName($model);
    }
}