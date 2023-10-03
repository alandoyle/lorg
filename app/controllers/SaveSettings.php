<?php
/**
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the SaveSettings Controller.
 */

class SaveSettingsController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function execute($params)
    {
        parent::execute($params);

        $model = $this->LoadModel($this->modelName);
        $model->readData($_REQUEST);

        $view = $this->LoadView($this->viewName, $model);
        $view->renderView();
    }
}