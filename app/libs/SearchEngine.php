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
    private $api_enabled = true;
    private $searchtype  = SEARCH_TEXT;

    // Handles
    private $search_ch  = NULL;
    private $special_ch = NULL;
    private $mh         = NULL;

    // Paramaters
    private $query   = '';
    private $type    = SEARCH_TEXT;
    private $pagenum = 0;

    // Results
    private $http_status     = 0;
    private $search_url = '';
    private $search_results  = [];
    private $special_results = [];

    public function __construct($api_enabled)
    {
        $this->api_enabled = $api_enabled;
        $this->mh          = curl_multi_init();
    }

    public function Query($query, $type, $pagenum, &$config)
    {
        $this->query   = $query;
        $this->type    = $type;
        $this->pagenum = $pagenum;

        // Clear before starting
        $this->http_status = 0;
        $count = 0;

        // Set search type
        $this->searchtype = ($this->api_enabled == true) ? SEARCH_API : $type;

        // Set max loops
        $maxloops = ($this->api_enabled == true) ? 1 : 10;

        // Loop up to 10 times (prevent infinite loops) to get the results.
        while (($this->http_status != 200) && ($count < $maxloops)) {
            // Clear Handles
            $this->search_ch  = NULL;
            $this->special_ch = NULL;

            switch($this->searchtype)
            {
                case SEARCH_TEXT:  // Text Search
                    $this->special_ch = SpecialEngine::Init($this->mh, $query, $type, $pagenum, $config);
                    // Fall-thru to Google Search
                case SEARCH_IMAGE: // Image Search
                    $this->search_ch = GoogleEngine::Init($this->mh, $query, $type, $pagenum, $config);
                    break;
                case SEARCH_VIDEO: // Video Search
                    $this->search_ch = InvidiousEngine::init($this->mh, $query, $type, $pagenum, $config);
                    break;
                case SEARCH_API: // API Searc
                    $this->special_ch = SpecialEngine::Init($this->mh, $query, $type, $pagenum, $config);
                    $this->search_ch  = ApiEngine::Init($this->mh, $query, $type, $pagenum, $config);
                   break;
            }
            $count++;

            // Download everything in the background
            $running = null;
            do {
                curl_multi_exec($this->mh, $running);
            } while ($running);
            $this->http_status = curl_getinfo($this->search_ch, CURLINFO_RESPONSE_CODE);

            // Get Results
            switch ($this->http_status)
            {
                case 200:
                    // Read Search Results
                    $this->getResults($this->search_ch,
                                      $this->query,
                                      $this->type,
                                      $this->pagenum,
                                      $config);
                    // Occasionally we get 0 responses from Google so we retry.
                    if (count($this->search_results) == 0) {
                        $this->http_status = 503;
                    }
                    break;
                case 302:
                    // We're Rate-Limited so slow down a little
                    sleep (1);
                default:
                    break;
            }

            //close the handles
            if ($this->special_ch != NULL) curl_multi_remove_handle($this->mh, $this->special_ch);
            if ($this->search_ch  != NULL) curl_multi_remove_handle($this->mh, $this->search_ch);
            curl_multi_close($this->mh);
        }

        // Generate a FAKE search response which includes a link to click.
        if ($this->http_status != 200) {
            // Build the URL
            $url = $config['base_url'].'/search?q=';
            if (! empty($this->query))   { $url .= urlencode($this->query); }
            if (! empty($this->type))    { $url .= '&t='.$this->type;       }
            if (! empty($this->pagenum)) { $url .= '&p='.$this->pagenum;    }

            array_push($this->search_results,
                    array (
                        "title"       => "Instance Rate-Limited",
                        "sitename"    => $config['opensearch_title'],
                        "image"       => get_blank_image(),
                        "url"         => $url,
                        "base_url"    => $config['base_url'],
                        "description" => "Instance Rate-Limited. Please 'Refresh' to try again in  a few seconds."
                    )
                );
        }
    }

    public function GetHttpStatus()
    {
        return $this->http_status;
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
        switch($this->searchtype)
        {
            case SEARCH_TEXT:  // Text Search
                // Read Special Results
                $this->special_results = SpecialEngine::GetResults($this->special_ch, $query, $type, $pagenum, $config);
            case SEARCH_IMAGE: // Image Search
                $this->search_results = GoogleEngine::GetResults($search_ch, $query, $type, $config);
                break;
            case SEARCH_VIDEO: // Video Search
                $this->search_results = InvidiousEngine::GetResults($search_ch, $query, $type, $config);
                break;
            case SEARCH_API: // API Search
                $this->special_results = SpecialEngine::GetResults($this->special_ch, $query, $type, $pagenum, $config);
                $this->search_results  = ApiEngine::GetResults($search_ch, $query, $type, $config);
                break;
            }
    }
}