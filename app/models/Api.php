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
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params)
    {
        $this->getBaseData($params);
        $this->data['output'] = 'json';

        if (empty($this->data['query'])) {
            // Show the HTML page.
            $this->data['output'] = 'html';
            return;
        }

        $query   = $this->data['query'];
        $type    = $this->data['type'];
        $pagenum = $this->data['pagenum'];

        $mh = curl_multi_init();

        $search_ch  = SearchEngine::Init($mh, $query, $type, $pagenum, $this->config);

        // Download everything in the background
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running);

        // Get search results
        $results = SearchEngine::GetResults($search_ch, $query, $type, $pagenum, $this->config);
//@@@debug_array($results);

        // Get JSON results.
        $filedata = json_encode($results, JSON_PRETTY_PRINT);

        // Query failed so we send a JSON error
        if (empty($results)) {
            $error = [ 'success' => 1, 'message' => 'ERROR: Unable to produce JSON search results.'];
            $filedata = json_encode($error);
        }

        $this->data['data'] = $filedata;
    }
}