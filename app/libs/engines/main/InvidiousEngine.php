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
 * This is the Invidious Search Engine.
 *
 ***************************************************************************************************
 */

 class InvidiousEngine {

    static function Init($mh, $query, $type, $pagenum, &$config)
    {
        // Start building the URL.
        $instance_url  = $config['invidious_url'];
        $query_encoded = urlencode($query);
        $pagenum       = $pagenum + 1;
        $url           = "$instance_url/api/v1/search?q=$query_encoded&page=$pagenum";

        // Save the URL
        $config['search_url'] = $url;

        $invidious_ch = curl_init($url);
        curl_setopt_array($invidious_ch, get_curl_options($config['ua'], $config['accept_langauge']));
        curl_setopt($invidious_ch, CURLOPT_USERAGENT, $config['ua']);
        curl_multi_add_handle($mh, $invidious_ch);

        return $invidious_ch;
    }

    static function GetResults($search_ch, $query, $type, &$config)
    {
        $results       = [];
        $webresponse   = curl_multi_getcontent($search_ch);
        $json_response = json_decode($webresponse, true);
        $instance_url  = $config['invidious_url'];
        $resultcount   = 0;

        foreach ($json_response as $response)
        {
            if ($response["type"] == "video")
            {
                $title = $response["title"];
                $url = "https://youtube.com/watch?v=" . $response["videoId"];
                $uploader = $response["author"];
                $views = $response["viewCount"];
                $date = $response["publishedText"];
                $thumbnail = "$instance_url/vi/" . explode("/vi/", $response["videoThumbnails"][4]["url"])[1];

                array_push($results,
                    array (
                        "title"     => htmlspecialchars($title),
                        "url"       => htmlspecialchars($url),
                        "base_url"  => htmlspecialchars(get_base_url($url)),
                        "uploader"  => htmlspecialchars($uploader),
                        "views"     => htmlspecialchars($views),
                        "date"      => htmlspecialchars($date),
                        "thumbnail" => get_image_url($thumbnail, $config)
                    )
                );
                $resultcount++;
            }
        }

        $config['result_count'] = $resultcount;

        return $results;
    }

    static function getEngineName()
    {
        return "Invidious";
    }
}