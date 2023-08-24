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
 * This is the Url Controller.
 *
 ***************************************************************************************************
 */

class UrlController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function execute($params = [])
    {
        $model = $this->loadModel($this->modelName);
        $model->readData($params);

        $view = $this->loadView($this->viewName, $model);
        $view->renderView();
    }
}