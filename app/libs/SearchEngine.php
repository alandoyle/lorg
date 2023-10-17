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
    private $api_disabled = false;

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
        $this->api_disabled = $config['api_disabled'];
        $this->config       = $config;
        $this->mh           = curl_multi_init();
    }

    public function Init($query, $type, $pagenum)
    {
        $this->query   = $query;
        $this->type    = $type;
        $this->pagenum = $pagenum;

        // Check if we're using API servers.
        if ($this->api_disabled == false) {
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
        while (($http_status != '200') && ($count < 10)) {
            $count++;

            // Download everything in the background
            $running = null;
            do {
                curl_multi_exec($this->mh, $running);
            } while ($running);

            $http_status = curl_getinfo($this->search_ch, CURLINFO_RESPONSE_CODE);

            // Check if we're Rate-Limited
            if (($this->api_disabled) &&
                ($http_status == '302')) {
                sleep (1);
                continue;
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
                }
            }
        }

        // Generate a FAKE search response which includes a link to click.
        if ($http_status != '200') {
            // Build the URL
            $url = $this->config['base_url'].'/search?q=';
            if (is_array($_REQUEST)) {
                if (array_key_exists($_REQUEST, 'q')) { $url .= urlencode($_REQUEST['q']); }
                if (array_key_exists($_REQUEST, 't')) { $url .= '&t='.$_REQUEST['t']; }
                if (array_key_exists($_REQUEST, 'p')) { $url .= '&p='.$_REQUEST['p']; }
            }

            array_push($this->search_results,
                    array (
                        "title"       => "Instance Rate-Limited",
                        "sitename"    => $this->config['opensearch_title'],
                        "image"       => get_blank_image(),
                        "url"         => $url,
                        "base_url"    => $this->config['base_url'],
                        "description" => "Instance Rate-Limited. Please 'Refresh' to try again in  a few seconds."
                    )
                );
        }
    }

    public function GetEngineName($type)
    {
        switch($type)
        {
            case SEARCH_TEXT:  // Text Search
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
        if ($this->api_disabled == false) {
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