<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * App Router class
 * Creates URL and loads controller
 * URL FORMAT  /index.php?r=<route>&m=<method>&<...querystring>
 *
 **************************************************************************************************/

/**
 * Main Router class of the MVC framework
 *
 ***************************************************************************************************
 * Properties
 * ==========
 * @property-read string $defaultControllerFilename
 * @property-read string $defaultControllerClassName
 * @property-read string $defaultMethod
 * @property-read string $params
 *
 ***************************************************************************************************
 * Public Methods
 * ==============
 * @method void __construct(string $querystring, string $basedir)
 *
 ***************************************************************************************************
 * Private Methods
 * ===============
 * @method string genControllerFilename(string $query)
 * @method string getControllerClassName(string $controllerName)
 * @method bool isValid(string $controllerName, string $basedir)
 *
 **************************************************************************************************/
class Router
{
    protected $defaultControllerFilename = 'Index';
    protected $defaultControllerClassName = 'IndexController';
    protected $defaultMethod = 'execute';
    protected $params = [];

    /**
     * Router constructor.
     *
     * @param string $querystring
     * @param string $basedir
     * @return void
     */
    public function __construct($querystring, $basedir)
    {
        /*******************************************************************************************
         * Build args
         ******************************************************************************************/
        $queryarray = explode('&',html_entity_decode($querystring));
        foreach ($queryarray as $value) {
            $newarg = explode('=', $value);
            if (count($newarg) === 2) {
                $this->params[$newarg[0]] = urldecode($newarg[1]);
            }
        }

        /*******************************************************************************************
         * Store the Base Directory
         ******************************************************************************************/
        $this->params['_basedir_'] = $basedir;

        /*******************************************************************************************
         * Get Controller name to use.
         ******************************************************************************************/
        $currentControllerFilename  = $this->genControllerFilename(array_key_exists('r', $this->params) ?
                                             $this->params['r'] :
                                             $this->defaultControllerFilename);
        $currentControllerClassName = $this->getControllerClassName($currentControllerFilename);

        /*******************************************************************************************
         * Get the method to call
         ******************************************************************************************/
        $currentMethod = array_key_exists('m', $this->params) ? $this->params['m'] : $this->defaultMethod;

        /*******************************************************************************************
         * Check if Controller exists, otherwise use default Controller (Index)
         ******************************************************************************************/
        if ($this->isValid($currentControllerFilename, $basedir) === false) {
            /***************************************************************************************
             * Goto Default route (Index)
             **************************************************************************************/
            $currentControllerFilename  = $this->defaultControllerFilename;
            $currentControllerClassName = $this->defaultControllerClassName;
        }
        $controllerPath = "$basedir/app/controllers/$currentControllerFilename.php";

        /*******************************************************************************************
         * Require controllers
         ******************************************************************************************/
        require_once $controllerPath;
        $currentController = new $currentControllerClassName;

        /*******************************************************************************************
         * Check if method exists in controller
         ******************************************************************************************/
        if (method_exists($currentController, $currentMethod) === false) {
            http_response_code(501);
            die("ERROR: Controller '$currentControllerFilename' is missing the '$currentMethod()' method!");
        }

        /*******************************************************************************************
         * Call a callback with array of params
         ******************************************************************************************/
        call_user_func_array([
            $currentController,
            $currentMethod
            ], [$this->params]);
    }

    /**
     * Generate a Controller filename from the incoming route.
     *
     * @param string $route
     * @return string
     */
    private function genControllerFilename($route)
    {
        $url        = explode('/', $route);
        $controller = $url[0];

        /*******************************************************************************************
         * Possibly a Template file so save the filename.
         *  e.g. /template/css/style.css
         ******************************************************************************************/
        if (count($url) > 1) {
            // Remove the route element
            unset($url[0]);

            // Build the full path from theremaining URL elements
            $filename = '';
            foreach ($url as $path) {
                $filename .= trim("$path/");
            }
            $this->params['f'] = substr_replace($filename ,"", -1);
        }
        $controllerFilename = $controller;
        $controllerFilename = ucwords($controllerFilename, " \t\r\n\f\v.");
        $controllerFilename = str_replace('.', '', $controllerFilename);

        return $controllerFilename;
    }

    /**
     * Generate a Controller class name.
     *
     * @param string $controllerName
     * @return string
     */
    private function getControllerClassName($controllerName)
    {
        return $controllerName."Controller";
    }

    /**
     * Determines if the Controller is a valid one.
     *
     * @param string $controllerName
     * @param string $basedir
     * @return bool
     */
    private function isValid($controllerName, $basedir)
    {
        $controllerFilename = $controllerName.'.php';
        $files = glob("$basedir/app/controllers/*.php");

        foreach ($files as $path) {
            $filename = basename($path);
            if (strcmp($controllerFilename, $filename) == 0) {
                return true;
            }
        }
        return false;
    }
}