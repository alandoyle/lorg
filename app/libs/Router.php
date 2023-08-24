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
 * URL FORMAT  /<controller>?q=<query>&p=<pagenum>&t=<type>
 *
 ***************************************************************************************************
 */

class Router
{
    protected $defaultControllerName = 'Index';
    protected $defaultControllerClass = 'IndexController';
    protected $params = [];

    public function __construct($querystring)
    {
        // Build args
        $queryarray = explode('&',html_entity_decode($querystring));
        foreach ($queryarray as $value) {
            $newarg = explode('=', $value);
            if (count($newarg) === 2) {
                $this->params[$newarg[0]] = urldecode($newarg[1]);
            }
        }

        // Get Controller name to use.
        $currentControllerName  = $this->genControllerName(array_key_exists('r', $this->params) ? $this->params['r'] : $this->defaultControllerName);
        $currentControllerClass = $this->getControllerClassName($currentControllerName);

        $currentMethod = 'execute'; // Only method supported (so far)

        // Check if Controller exists, otherwise use default Controller (Index)
        if ($this->isValid($currentControllerName) === false) {
            // Goto Default route (Index)
            $currentControllerName  = $this->defaultControllerName;
            $currentControllerClass = $this->defaultControllerClass;
        }
        $controller = '../app/controllers/'.$currentControllerName.'.php';

        //require controllers
        require_once $controller;
        $currentController = new $currentControllerClass;

        //check if method exists in controller
        if (method_exists($currentController, $currentMethod) === false) {
            http_response_code(501);
            die("ERROR: Controller ".$currentControllerName." is missing the ".$currentMethod."() method!");
        }

        // call a callback with array of params
        call_user_func_array([
            $currentController,
            $currentMethod
            ], [$this->params]);
    }

    private function genControllerName($query)
    {
        $url = explode('/', $query);

        // Possibly a Custom file so save the filename.
        // e.g. /custom/custom.css
        if (count($url) > 1) {
            $this->params['f'] = $url[1];
        }

        $controllerName = $url[0];
        $controllerName = ucwords($controllerName, " \t\r\n\f\v.");
        $controllerName = str_replace('.', '', $controllerName);

        return $controllerName;
    }

    private function getControllerClassName($controllerName)
    {
        return $controllerName."Controller";
    }

    private function isValid($controllerName)
    {
        $controllerFilename = $controllerName.'.php';
        $files = glob('../app/controllers/*.php');

        foreach ($files as $path) {
            $filename = basename($path);
            if (strcmp($controllerFilename, $filename) == 0) {
                return true;
            }
        }
        return false;
    }
}