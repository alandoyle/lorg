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

    static function Init($mh, $query, $type, $pagenum, $config)
    {
        $use_qwant_for_images     = $config['use_qwant_for_images'];
        $use_invidious_for_videos = $config['use_invidious_for_videos'];
        $search_ch                = NULL;

        switch($type)
        {
/*
            case SEARCH_IMAGE: // Image Search
                if ($use_qwant_for_images === true) {
                    $search_ch = QwantEngine::getUrl($mh, $query, $type, $pagenum);
                }
            case SEARCH_VIDEO: // Video Search
                if ($use_invidious_for_videos === true) {
                    $search_ch = InvidiousEngine::getUrl($mh, $query, $type, $pagenum, $config);
                }
*/
            case SEARCH_TEXT: // Text Search
            default:
                $search_ch = GoogleEngine::init($mh, $query, $type, $pagenum, $config);
        }

        return $search_ch;
    }

    static function GetResults($search_ch, $query, $type, $pagenum, $config)
    {
        $use_qwant_for_images     = $config['use_qwant_for_images'];
        $use_invidious_for_videos = $config['use_invidious_for_videos'];

        switch($type)
        {
/*
            case SEARCH_IMAGE: // Image Search
                if ($use_qwant_for_images === true) {
                    return QwantEngine::getResults($query, $type, $pagenum);
                }
            case SEARCH_VIDEO: // Video Search
                if ($use_invidious_for_videos === true) {
                    return InvidiousEngine::getResults($query, $type, $pagenum);
                }
*/
            case SEARCH_TEXT: // Text Search
            default:
                return GoogleEngine::getResults($search_ch, $query, $type, $config);
        }
    }

    static function GetEngineName($type, $config)
    {
        $use_qwant_for_images     = $config['use_qwant_for_images'];
        $use_invidious_for_videos = $config['use_invidious_for_videos'];

        switch($type)
        {
            case SEARCH_IMAGE: // Image Search
                if ($use_qwant_for_images === true) {
                    return QwantEngine::getEngineName();
                }
            case SEARCH_VIDEO: // Video Search
                if ($use_invidious_for_videos === true) {
                    return InvidiousEngine::getEngineName();
                }
            case SEARCH_TEXT: // Text Search
            default:
                return GoogleEngine::getEngineName();
        }
    }
}