<?php
/**
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Index Controller.
 */

class IndexController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function execute($params)
    {
        parent::execute($params);

        //@@@ TODO - Implement ErrorController???
        /*******************************************************************************************
         * Index Controller only displays at the base_url
         ******************************************************************************************/
        if (empty($params['q']) !== true) {
            $this->RedirectToURL($this->config['base_url']);
        }

        $model = $this->LoadModel($this->modelName);
        $model->readData($params);

        $view = $this->LoadView($this->viewName, $model);
        $view->renderView();
    }
}