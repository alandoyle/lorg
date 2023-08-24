<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the custom `files` View.
 *
 ***************************************************************************************************
 */
class CustomView extends View {
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function renderView()
    {
        $this->renderFileData($this->model->getData());
    }
}