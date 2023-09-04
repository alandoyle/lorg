<?php
/**
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Search Controller.
 */

class SearchController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function execute($params)
    {
        $query = array_key_exists('q', $params) ? $params['q'] : '';

        // Redirect to base URL if no query
        if (strlen($query) == 0) {
            $this->RedirectToURL($this->config['base_url']);
        }

        $model = $this->LoadModel($this->modelName);
        $model->readData($params);

        $view = $this->LoadView($this->viewName, $model);
        $view->renderView();
    }
}