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

    static function Init(&$mh, $query, $type, $pagenum, &$config)
    {
        $query_encoded = urlencode($query);
        $api_server = ApiEngine::getRandomApiServer($config);
        $url =  "$api_server?q=$query_encoded&p=$pagenum&t=$type";

        // Save the URL
        $config['api_url'] = $url;

        $api_ch = curl_init($url);
        curl_setopt_array($api_ch, get_curl_options($config['ua'], $config['accept_langauge']));
        curl_setopt($api_ch, CURLOPT_USERAGENT, $config['ua']);
        curl_multi_add_handle($mh, $api_ch);

        return $api_ch;
    }

    static function GetResults($search_ch, $query, $type, &$config)
    {
        // Decode the JSON response.
        $webresponse   = curl_multi_getcontent($search_ch);
        $json_response = json_decode($webresponse, true);
        if (!$json_response) {
            return [];
        }
        $results = $json_response['results'];

        $config['result_count'] = count($results);
        $config['search_url'] = $json_response['search_url'];

        return $results;
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

        return $instance['URL'];
    }
}