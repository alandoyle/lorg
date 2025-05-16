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
 * This is the Qwant Search Engine.
 *
 ***************************************************************************************************
 */

class QwantEngine {

    static function Init($mh, $query, $type, $pagenum, &$config)
    {
        $query_encoded  = urlencode($query);
        $offset         = $pagenum * 5; // load 50 images per page

        $url = "https://api.qwant.com/v3/search/images?q=$query_encoded&t=images&count=50&locale=en_us&offset=$offset&device=desktop&tgp=3";

        // Check if "Safe Search" has been enabled in the settings.
        if (isset($_COOKIE['safe_search'])) {
            $url .= '&safesearch=1';
        }

        // Save the URL
        $config['search_url'] = $url;

        $qwant_ch = curl_init($url);
        curl_setopt_array($qwant_ch, get_curl_options($_SERVER["HTTP_USER_AGENT"], $config['accept_langauge']));
        curl_setopt($qwant_ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_multi_add_handle($mh, $qwant_ch);

        return $qwant_ch;
    }

    static function getEngineName()
    {
        return "Qwant";
    }

    static function GetResults($search_ch, $query, $type, &$config)
    {
        if (curl_getinfo($search_ch)['http_code'] == '403') {
            //@@@ TODO Try another instance
            //die();
        }

        $results     = [];
        $webresponse = curl_multi_getcontent($search_ch);

        $json = json_decode($webresponse, true);
        $resultcount = 0;

        if ($json["status"] != "success") {
            return $results; // no results
        }


        $imgs = $json["data"]["result"]["items"];
        $imgCount = $json["data"]["result"]["total"];

        for ($i = 0; $i < $imgCount; $i++)
        {
            $siteurl  = htmlspecialchars($imgs[$i]["url"]);
            $parse = parse_url($siteurl);
            $url   = $parse['scheme'] . "://" . $parse['host'];
            array_push($results, 
                array (
                    "title"     => htmlspecialchars($imgs[$i]["title"]),
                    "sitename"  => $url,
                    "thumbnail" => get_image_url(urlencode($imgs[$i]["thumbnail"]), $config),
                    "url"       => $siteurl
                )
            );
        }
        $config['result_count'] = $resultcount;

        return $results;
    }
}