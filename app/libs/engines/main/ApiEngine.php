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
 * This is the API Search Engine.
 *
 ***************************************************************************************************
 */

 class ApiEngine {

    static function init($mh, $query, $type, $pagenum, &$config)
    {
        ApiEngine::getRandomApiServer($config);

        $search_ch           = NULL;
        $query_encoded       = urlencode($query);
        $config['searchurl'] = $config['api_url']."?q=$query_encoded&p=$pagenum&t=$type&key=".$config['api_key'];
        $search_ch           = curl_init($config['searchurl']);
        $curl_options        = get_curl_options($config['ua'], $config['accept_langauge']);

        curl_setopt_array($search_ch, $curl_options);
        curl_setopt($search_ch, CURLOPT_USERAGENT, $config['ua']);
        curl_multi_add_handle($mh, $search_ch);

        return $search_ch;
    }

    static function Retry($mh, $search_ch, $query, $type, $pagenum, &$config)
    {
        $search_ch = NULL;

        ApiEngine::getRandomApiServer($config);

        return $search_ch;
    }

    static function GetResults($search_ch, $query, $type, $pagenum, &$config)
    {
        // Decode the JSON response.
        $webresponse   = curl_multi_getcontent($search_ch);
        $json_response = json_decode($webresponse, true);
        if (!$json_response) {
            return [];
        }

        $config['result_count'] = count($json_response);
        return $json_response;
    }

    /**
     * Return a random API URL from an array.
     *
     * @param array $config
     * @return none
     */
    static function getRandomApiServer(&$config)
    {
        /*******************************************************************************************
         * Set the random API Server Instance
         ******************************************************************************************/

        $instances = $config['api_servers'];
        $instance  = $instances[array_rand($instances, 1)];

        $config['api_url'] = $instance['URL'];
    }
}