<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the `opensearch.xml` Controller.
 *
 ***************************************************************************************************
 */

class OpensearchXmlController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $model = $this->loadModel($this->modelName);
        $model->readData();

        $view = $this->loadView($this->viewName, $model);
        $view->renderView();
    }
}