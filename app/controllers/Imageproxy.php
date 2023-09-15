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
 * This is the Imageproxy Controller.
 *
 ***************************************************************************************************
 */

class ImageproxyController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function execute($params)
    {
        parent::execute($params);

        $model = $this->LoadModel($this->modelName);
        $model->readData($params);

        $view = $this->LoadView($this->viewName, $model);
        $view->renderView();
    }
}