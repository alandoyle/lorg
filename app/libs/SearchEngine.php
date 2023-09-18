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
 ***************************************************************************************************
 */

 class SearchEngine {

    static function Init($mh, $query, $type, $pagenum, &$config)
    {
        $search_ch = NULL;

        switch($type)
        {
            case SEARCH_TEXT:  // Text Search
            case SEARCH_IMAGE: // Image Search
                $search_ch = GoogleEngine::init($mh, $query, $type, $pagenum, $config);
                break;
            case SEARCH_VIDEO: // Video Search
                $search_ch = InvidiousEngine::init($mh, $query, $type, $pagenum, $config);
                break;
        }

        return $search_ch;
    }

    static function GetResults($search_ch, $query, $type, $pagenum, &$config)
    {
        switch($type)
        {
            case SEARCH_TEXT:  // Text Search
            case SEARCH_IMAGE: // Image Search
                return GoogleEngine::getResults($search_ch, $query, $type, $config);
            case SEARCH_VIDEO: // Video Search
                return InvidiousEngine::getResults($search_ch, $query, $type, $config);
        }
    }

    static function GetEngineName($type, $config)
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
}