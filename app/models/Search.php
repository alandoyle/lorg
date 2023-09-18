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
 * This is the Search Model.
 *
 ***************************************************************************************************
 */

 class SearchModel extends Model {

    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function readData($params)
    {
        $this->getBaseData($params);
        $this->getCategories($this->data['type']);
        $this->getSearchResults($this->data['query'], $this->data['type'], $this->data['pagenum']);
    }

    private function getCategories($active)
    {
        // Build up the Category details
        $mappings = array("general", "images", "youtube");
        $categories = [];
        foreach ($mappings as $category)
        {
            //echo $category;
            $category_index = array_search($category, $mappings);
            $categories[$category_index] = [
                'class'       => ($category_index == $active) ? 'class="active" ' : '',
                'type'        => $category_index,
                'typename'    => $category,
                'description' => ucfirst($category),
            ];
        }
        $this->data['categories'] = $categories;
    }

    private function getSearchResults($query, $type, $pagenum)
    {
        $mh = curl_multi_init();

        $start_time = microtime(true);

        $search_ch  = SearchEngine::Init($mh, $query, $type, $pagenum, $this->config);
        $special_ch = SpecialEngine::Init($mh, $query, $type, $pagenum, $this->config);

        // Download everything in the background
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running);

        if ($search_ch !== null) {
            if (curl_getinfo($search_ch)['http_code'] == '302') {
                //@@@ TODO Try another instance
                echo curl_multi_getcontent($search_ch);
                die();
            }
        }

        // Get search results
        $this->data['results']      = SearchEngine::GetResults($search_ch, $query, $type, $pagenum, $this->config);
        $this->data['searchurl']    = $this->config['search_url'];
        $this->data['result_count'] = $this->config['result_count'];

        // Only get Special results for Text searches, first page only.
        $this->data['special'] = SpecialEngine::GetResults($special_ch, $query, $type, $pagenum, $this->config);

        // Calculate time taken
        $this->data['end_time'] = number_format(microtime(true) - $start_time, 2, '.', '');
        $this->data['engine']   = SearchEngine::GetEngineName($type, $this->config);
        $this->data['maxpages'] = 10000; //@@@ REMOVEME
    }
}