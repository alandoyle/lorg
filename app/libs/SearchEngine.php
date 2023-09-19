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
 * This is the Search Engine.
 *
 **************************************************************************************************/

 class SearchEngine {

    // Configuration
    private $config;
    private $enable_api_servers = false;

    // Handles
    private $search_ch = NULL;
    private $special_ch = NULL;
    private $mh = NULL;

    // Paramaters
    private $query   = '';
    private $type    = SEARCH_TEXT;
    private $pagenum = 0;

    // Results
    private $search_results = [];
    private $special_results = [];

    public function __construct(&$config)
    {
        $this->enable_api_servers = $config['enable_api_servers'];
        $this->config             = $config;
        $this->mh                 = curl_multi_init();
    }

    public function Init($query, $type, $pagenum)
    {
        $this->query   = $query;
        $this->type    = $type;
        $this->pagenum = $pagenum;

        // Check if we're using API servers.
        if ($this->enable_api_servers) {
            debug_var("API: $type");
            $this->search_ch = ApiEngine::init($this->mh, $query, $type, $pagenum, $this->config);
            return;
        }

        switch($type)
        {
            case SEARCH_TEXT:  // Text Search
                $this->special_ch = SpecialEngine::Init($this->mh, $query, $type, $pagenum, $this->config);
            case SEARCH_IMAGE: // Image Search
                $this->search_ch = GoogleEngine::init($this->mh, $query, $type, $pagenum, $this->config);
                break;
            case SEARCH_VIDEO: // Video Search
                $this->search_ch = InvidiousEngine::init($this->mh, $query, $type, $pagenum, $this->config);
                break;
        }
    }

    public function RunQuery()
    {
        $http_status = '000';
        $count = 0;

        // Loop up to 10 times (prevent infinite loops) to get the results.
        while (($http_status != '200') &&
               ($count < 10)) {
            $count++;

            // Download everything in the background
            $running = null;
            do {
                curl_multi_exec($this->mh, $running);
            } while ($running);

            $http_status = curl_getinfo($this->search_ch, CURLINFO_RESPONSE_CODE);

            // If we haven't got any API servers then we need to succeed even if we fail :(
            if (($this->enable_api_servers === false) &&
                ($http_status != '302')) {
                $http_status = '200';
            }

            if ($http_status == '200') {
                // Read Search Results
                $this->search_results = $this->getResults($this->search_ch,
                                                          $this->query,
                                                          $this->type,
                                                          $this->pagenum,
                                                          $this->config);
                // Occasionally we get 0 responses from Google so we retry.
                if (count($this->search_results) == 0) {
                    $http_status = '503';
                    debug_var("RETRYING");
                }
            }
        }
    }

    public function GetEngineName($type)
    {
        switch($type)
        {
            case SEARCH_TEXT: // Text Search
            case SEARCH_IMAGE: // Image Search
                return GoogleEngine::getEngineName();
            case SEARCH_VIDEO: // Video Search
                return InvidiousEngine::getEngineName();
        }
    }

    public function GetSearchResults()
    {
        return $this->search_results;
    }

    public function GetSpecialResults()
    {
        return $this->special_results;
    }

    private function getResults($search_ch, $query, $type, $pagenum, &$config)
    {
        // Check if we're using API servers.
        if ($this->enable_api_servers) {
            debug_var("API: $type");
            $this->search_ch = ApiEngine::init($this->mh, $query, $type, $pagenum, $this->config);
            return;
        }

        switch($type)
        {
            case SEARCH_TEXT:  // Text Search
                // Read Special Results
                $this->special_results = SpecialEngine::GetResults($this->special_ch,
                                                                   $query,
                                                                   $type,
                                                                   $pagenum,
                                                                   $config);
            case SEARCH_IMAGE: // Image Search
                return GoogleEngine::getResults($search_ch, $query, $type, $config);
            case SEARCH_VIDEO: // Video Search
                return InvidiousEngine::getResults($search_ch, $query, $type, $config);
        }
    }
}