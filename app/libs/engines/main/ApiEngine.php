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
        $count = 0;
        $found = false;
        $instances = $config['api_servers'];
        $basedir   = $config['basedir'];

        do {
            $instance = $instances[array_rand($instances, 1)];
            $keyfile  =
            $contents = '';

            switch (strtolower($instance['Type']))
            {
                case 'local':
                    $keyfile = 'api.key';
                    break;
                case 'remote':
                    $keyfile = $instance['Name'].'.key';
                    break;
            }

            /*******************************************************************************************
             * Load the API Key.
             ******************************************************************************************/
            if (file_exists("$basedir/config/keys/$keyfile")) {
                $contents  = file_get_contents("$basedir/config/keys/$keyfile");
                if (strlen(trim($contents)) > 0) {
                    $config['api_url'] = $instance['URL'];
                    $config['api_key'] = trim($contents);
                    $found = true;
                }
            }

            /*******************************************************************************************
             * Remove duff entries from the instances list.
             ******************************************************************************************/
            if ($found === false) {
                for ($n = 0; $n < count($instances); $n++) {
                    if ($instances[$n]['Name'] === $instance['Name']) {
                        unset($instances[$n]);
                        $instances = array_values($instances);
                        break;
                    }
                }
            }
            $count++;
        }
        while(($found === false) && ($count < 5));
    }
}