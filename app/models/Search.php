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

    public function __construct($basedir)
    {
        parent::__construct($basedir);
    }

    public function readData(&$config, $params = [])
    {
        parent::readData($config, $params);
        $this->getCategories($this->data['type']);
        $this->getSearchResults($this->data['query'], $this->data['type'], $this->data['pagenum'], $config);
    }

    private function getCategories($active)
    {
        // Build up the Category details
        $mappings = array("general", "images", "youtube");
        $categories = [];
        foreach ($mappings as $category)
        {
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

    private function getSearchResults($query, $type, $pagenum, &$config)
    {
        $start_time = microtime(true);

        $search_engine  = new SearchEngine($config['api_enabled']);
        $search_engine->Query($query, $type, $pagenum, $config);

        // Only get Special results for Text searches, first page only.
        $this->data['special']      = $search_engine->GetSpecialResults();

        // Get search results
        $this->data['results']      = $search_engine->GetSearchResults();
        $this->data['apiurl']       = array_key_exists('api_url', $config) ? $config['api_url'] : '';
        $this->data['searchurl']    = array_key_exists('search_url', $config) ? $config['search_url'] : '';
        $this->data['result_count'] = count($this->data['results']);

        // Calculate time taken
        $this->data['engine_name'] = $search_engine->GetEngineName($type);
        $this->data['end_time']    = number_format(microtime(true) - $start_time, 2, '.', '');
    }
}