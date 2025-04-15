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

        $ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0";
        $Qwant_ch = curl_init($url);
        curl_setopt_array($Qwant_ch, get_curl_options($ua, $config['accept_langauge']));
        curl_setopt($Qwant_ch, CURLOPT_USERAGENT, $ua);
        curl_multi_add_handle($mh, $Qwant_ch);

        return $Qwant_ch;
    }

    static function getEngineName()
    {
        return "Qwant";
    }

    static function GetResults($search_ch, $query, $type, &$config)
    {
//@@@debug_var("Parsing Qwant Image Results.");

        if (curl_getinfo($search_ch)['http_code'] == '403') {
            //@@@ TODO Try another instance
            //die();
/*@@@
            {"status":"error","data":{"error_data":{"captchaUrl":"https://geo.captcha-delivery.com/captcha/?initialCid=AHrlqAAAAAMADk6MwHt8tNoAP4dJDw==&cid=5GDDOC4i7WSxKN~fmU4QMhMAzfO61AQwhvYXx7njjN_BaMLvggeLdNM6uIZbazJChH~9aWUU9W67invL~DGUGoOppg~0L1cszNTkQ5yrY9kcUiV8ZFJ1rhhlpcQ7xUwf&referer=http%3A%2F%2Ffdn.qwant.com%2Fv3%2Fsearch%2Fimages%3Fq%3Ddf6715ef32ab826b200d9367771abc34bb1abe5a66401d99df8a58e34cdc4758f52d7810248b34694df1c0b63faf7fa550489ac247db331053442c73d6faa238%26t%3Dimages%26count%3D50%26locale%3Den_us%26offset%3D0%26device%3Ddesktop%26tgp%3D3&hash=78B13B7513D180B7AB6D6FF9EB0A51&t=fe&s=48947&e=310b96b98ec7c5dd872966467be9367c17f0a9e71ee482de951716ece729fdf6"},"error_code":27}}
*/
//@@@debug_var("QWANT: Forbidden");
        }

        $results     = [];
        $webresponse = curl_multi_getcontent($search_ch);

        $json = json_decode($webresponse, true);
//@@@debug_array($json);
        $resultcount = 0;

        if ($json["status"] != "success") {
            return $results; // no results
        }


        $imgs = $json["data"]["result"]["items"];
        $imgCount = $json["data"]["result"]["total"];

        for ($i = 0; $i < $imgCount; $i++)
        {
            $imageurl = htmlspecialchars($imgs[$i]["media"]);
            $siteurl  = htmlspecialchars($imgs[$i]["url"]);
            $url = (($config['link_google_image'] === true) ?
                    get_image_url($imageurl, $config) :
                    $siteurl);
            array_push($results, 
                array (
                    "title"     => htmlspecialchars($imgs[$i]["title"]),
                    "sitename"  => "WIBBLE",
                    "thumbnail" => htmlspecialchars($imgs[$i]["thumbnail"]),
                    "url"       => $url
                )
            );
        }
        $config['result_count'] = $resultcount;

        return $results;
    }
}