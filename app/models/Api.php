<?php
/***************************************************************************************************
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * This is the Api Model.
 *
 ***************************************************************************************************
 */

 class ApiModel extends Model {
    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        parent::readData($config, $params);
        $this->data['output'] = 'json';

        if (empty($this->data['query'])) {
            // Show the HTML page.
            $this->data['output'] = 'html';
            return;
        }

        $query   = $this->data['query'];
        $type    = $this->data['type'];
        $pagenum = $this->data['pagenum'];

        // Create new SearchEngine object but ALWAYS set $api_enabled to FALSE to
        // prevent the API server recursively calling API servers.
        // The book stops here.
        $search_engine = new SearchEngine(false);
        $search_engine->Query($query, $type, $pagenum, $config);

        // Build the JSON
        $jsondata = [];
        $jsondata['http_status'] = $search_engine->GetHttpStatus();
        $jsondata['search_url']  = $config['search_url'];
        $jsondata['results']     = $search_engine->GetSearchResults();

        // Get JSON results.
        $filedata = json_encode($jsondata, ($config['minify_output'] == true ? JSON_MINIFY_PRINT : JSON_PRETTY_PRINT));

        $this->data['data'] = $filedata;
    }
}