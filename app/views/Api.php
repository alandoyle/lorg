<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @link https://github.com/alandoyle/lorg, https://lorg.dev
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 * This is the Api View.
 */

 class ApiView extends View {
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function renderView()
    {
        $redirect_url = empty($this->config['api_redirect_url']) ?
                              $this->config['base_url'] :
                              $this->config['api_redirect_url'];
        $data = $this->model->getData();
        switch($data['output'])
        {
        case 'html':
            if ($this->config['api_redirect'] === false) {
                $this->renderHtml('api.tpl', $data);
                return;
            }
            $this->RedirectToUrl($redirect_url);
            break;
        default:
            $this->renderJson($data);
            break;
        }
    }
}