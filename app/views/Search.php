<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Search View.
 *
 ***************************************************************************************************
 */

 class SearchView extends View {
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function renderView(&$config)
    {
        parent::renderView($config);

        $type = 0;
        $template = 'result-text.tpl';

        if (array_key_exists("type", $this->data)) {
            $type = $this->data['type'];
        }
        switch($type)
        {
            case 1:
                $template = 'result-image.tpl';
                break;
            case 2:
                $template = 'result-video.tpl';
                break;
            default:
                $template = 'result-text.tpl';
                break;
        }

        $this->renderHtml($template);
    }
}