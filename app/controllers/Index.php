<?php
/**
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Idex Controller.
 */

class IndexController extends Controller {
    public function __construct()
    {
        parent::__construct();
        
        if (empty($_SERVER['QUERY_STRING']) === false) {
            $this->redirect_to_url($this->config['base_url']);
        }
    }

    public function execute($params = [])
    {
        $model = $this->loadModel($this->modelName);
        $model->readData($params);

        $view = $this->loadView($this->viewName, $model);
        $view->renderView();
    }
}