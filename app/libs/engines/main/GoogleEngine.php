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
 * This is the Google Search Engine.
 *
 ***************************************************************************************************
 */

 class GoogleEngine {

    static function Init($mh, $query, $type, $pagenum, &$config)
    {
        $domain            = $config['google_domain'];
        $site_language     = isset($_COOKIE['google_language_site'])     ? trim(htmlspecialchars($_COOKIE['google_language_site']))     : $config['google_language_site'];
        $results_language  = isset($_COOKIE['google_language_results'])  ? trim(htmlspecialchars($_COOKIE['google_language_results']))  : $config['google_language_results'];
        $number_of_results = isset($_COOKIE['google_number_of_results']) ? trim(htmlspecialchars($_COOKIE['google_number_of_results'])) : $config['google_number_of_results'];
        $query_encoded     = urlencode($query);

        // Start building the URL.
        $url = "https://www.google.$domain/search?q=$query_encoded";

        switch($type)
        {
            case SEARCH_IMAGE: // Image Search
                $url .= "&oq=$query_encoded&tbm=isch&asearch=ichunk&async=_id:rg_s,_pms:s,_fmt:pc&sourceid=chrome&ie=UTF-8&ijn=$pagenum";
                break;
            case SEARCH_TEXT: // Text Search
                $startnum = $pagenum * 10;
                $url .= "&start=$startnum";
                break;
        }

        if (strlen($site_language) < 3 && strlen($site_language) > 0) {
            $url .= "&hl=$site_language";
        }

        if (strlen($results_language) < 3 && strlen($results_language) > 0) {
            $url .= "&lr=lang_$results_language";
        }

        if (strlen($number_of_results) < 3 && strlen($number_of_results) > 0) {
            $url .= "&num=$number_of_results";
        }

        // Check if "Safe Search" has been enabled in the settings.
        if (isset($_COOKIE['safe_search'])) {
            $url .= '&safe=medium';
        }

        // Save the URL
        $config['search_url'] = $url;

        $google_ch = curl_init($url);
        curl_setopt_array($google_ch, get_curl_options($config['ua'], $config['accept_langauge']));
        curl_setopt($google_ch, CURLOPT_USERAGENT, $config['ua']);
        curl_multi_add_handle($mh, $google_ch);

        return $google_ch;
    }

    static function GetResults($search_ch, $query, $type, &$config)
    {
        switch($type)
        {
            case SEARCH_IMAGE: // Image Search
                return GoogleEngine::getImageResults($search_ch, $query, $config);
            case SEARCH_TEXT: // Text Search
                return GoogleEngine::getTextResults($search_ch, $config);
            default:
                return [];
        }
    }

    static function getEngineName()
    {
        return "Google";
    }

    static function getTextResults($search_ch, &$config)
    {
        $results     = [];
        $webresponse = curl_multi_getcontent($search_ch);
        $blank_image = get_blank_image();
        $xpath       = get_xpath($webresponse);

        $resultcount = 0;
        $prevtitle   =
        $prevurl     =
        $previmage   = '';

        foreach($xpath->query("//div[@id='search']//div[contains(@class, 'g')]") as $result)
        {
            $classcount = count($xpath->evaluate(".//div[@class='yuRUbf']//a/@href", $result));
            if ($classcount == 0) {
                continue;
            }

            for ($x = 0; $x<$classcount; $x++) {
                // Reset prev image
                $previmage = ($x == 0) ? '' : $previmage;

                $url = $xpath->evaluate(".//div[@class='yuRUbf']//a/@href", $result)[$x];
                if ($url == null) {
                    continue;
                }

                $url         = $url->textContent;
                $title       = $xpath->evaluate(".//h3", $result)[$x];
                $description = $xpath->evaluate(".//div[contains(@class, 'VwiC3b')]", $result)[$x];
                $image       = $xpath->evaluate(".//img[contains(@class, 'XNo5Ab')]/@src", $result)[$x];
                $sitename    = $xpath->evaluate(".//span[contains(@class, 'VuuXrf')]", $result)[$x];
                $image_data  = $image == null ?
                                ($x > 0 ? $previmage : $blank_image) :
                                $image->textContent;
                $previmage   = $image_data;

                if (!empty($title)) {
                    $title = trim($title->textContent);
                }
                if (!empty($url)) {
                    $url = trim($url);
                }

                // Check if current result is a duplicate of the previous result.
                if (($title == $prevtitle) && ($url == $prevurl)) {
                    continue;
                } else {
                    array_push($results,
                        array (
                            "title"       => htmlspecialchars($title),
                            "sitename"    => $sitename == null ? "" : htmlspecialchars($sitename->textContent),
                            "image"       => $image_data,
                            "url"         => htmlspecialchars($url),
                            "base_url"    => htmlspecialchars(get_base_url($url)),
                            "description" => $description == null ?
                                                "No description was provided for this site." :
                                                htmlspecialchars($description->textContent)
                        )
                    );
                    $resultcount++;
                }

                // Save current title and URL as Google sometimes duplicates them.
                $prevtitle = $title;
                $prevurl   = $url;
            }
        }
        $config['result_count'] = $resultcount;

        return $results;
    }

    static function getImageURL($imageurl, $siteurl, $config)
    {
        return (($config['link_google_image'] === true) ?
                    get_image_url($imageurl, $config) :
                    $siteurl);
    }

    static function getImageResults($search_ch, $query, &$config)
    {
        if (curl_getinfo($search_ch)['http_code'] == '302') {
            //@@@ TODO Try another instance
            echo curl_multi_getcontent($search_ch);
            //die();
        }

        $results     = [];
        $webresponse = curl_multi_getcontent($search_ch);
        $xpath       = get_xpath($webresponse);
        if ($xpath == null) {
            return $results;
        }

        $resultcount = 0;
        foreach($xpath->query("//div[contains(@class, 'rg_meta')]") as $result)
        {
            $resultcount++;
            $json_response = json_decode($result->textContent, TRUE);
            $thumbnail = $json_response["tu"];

            $url = GoogleEngine::getImageURL($json_response["ou"], $json_response["ru"], $config);

            array_push($results,
                array (
                    "title"       => $json_response["pt"],
                    "sitename"    => $json_response["st"],
                    "thumbnail"   => get_image_url($thumbnail, $config),
                    "url"         => $url,
                )
            );
        }
        $config['result_count'] = $resultcount;

        return $results;
    }
}