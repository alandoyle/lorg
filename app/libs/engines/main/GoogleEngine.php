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

    static function init($mh, $query, $type, $pagenum, &$config)
    {
        $domain            = $config['google_domain'];
        $site_language     = isset($_COOKIE['google_language_site'])     ? trim(htmlspecialchars($_COOKIE['google_language_site']))     : $config['google_language_site'];
        $results_language  = isset($_COOKIE['google_language_results'])  ? trim(htmlspecialchars($_COOKIE['google_language_results']))  : $config['google_language_results'];
        $number_of_results = isset($_COOKIE['google_number_of_results']) ? trim(htmlspecialchars($_COOKIE['google_number_of_results'])) : $config['google_number_of_results'];
        $query_encoded     = urlencode($query);

        // Google Image search returns a MAX of 20 images.
        if ($type == SEARCH_IMAGE && $number_of_results > 20) {
            $number_of_results = 20;
        }

        // Start building the URL.
        $url = "https://www.google.$domain/search?q=$query_encoded";

        switch($type)
        {
            case SEARCH_IMAGE: // Image Search
                $url .= "&oq=$query_encoded&tbm=isch&asearch=ichunk&async=_id:rg_s,_pms:s,_fmt:pc&sourceid=chrome&ie=UTF-8&ijn=$pagenum";
                break;
            case SEARCH_VIDEO: // Video Search
                $url .= "&tbm=vid";
                break;
            case SEARCH_TEXT: // Text Search
            default:
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
            $url .= "&safe=medium";
        }

        // Save the URL
        $config['search_url'] = $url;

        $google_ch = curl_init($url);
        curl_setopt_array($google_ch, getCurlOptions());
        curl_setopt($google_ch, CURLOPT_USERAGENT, $config['ua']);
        curl_multi_add_handle($mh, $google_ch);

        return $google_ch;
    }

    static function getResults($search_ch, $query, $type, &$config)
    {
        switch($type)
        {
            case SEARCH_IMAGE: // Image Search
                return GoogleEngine::getImageResults($search_ch, $query, $config);
            case SEARCH_VIDEO: // Video Search
                return GoogleEngine::getVideoResults($search_ch, $query, $config);
            case SEARCH_TEXT: // Text Search
            default:
                return GoogleEngine::getTextResults($search_ch, $config);
        }
    }

    static function getEngineName()
    {
        return "Google";
    }

    //@@@ KILL This and replace with 0 results logic.
    static function getMaxResults($type)
    {
        switch($type)
        {
            /*case SEARCH_IMAGE: // Image Search
                return 3;
            case SEARCH_VIDEO: // Video Search
                return 3;*/
            case SEARCH_TEXT: // Text Search
            default:
                return 1000000;
        }
    }

    static function getTextResults($search_ch, &$config)
    {
        if (curl_getinfo($search_ch)['http_code'] == '302') {
            //@@@ TODO Try another instance
            echo curl_multi_getcontent($search_ch);
            //die();
        }

        $results     = [];
        $webresponse = curl_multi_getcontent($search_ch);
        $blank_image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwABGUAAARlAAYDjddQAAAOSSURBVEhLrdV5aM5xHMDxZ3PMMVf+cISZ3CY5IkeO3CIUJolyH5ErOfOHoyaEyJE/kKNNliNalMQi1x9irs3E3LTYjG3O9/vxPI+WLZRPvfx+m2ff7/fz+X6+3yfwt5GYOKZ86PWfIir0LDUYtC6PbriFDxiKGkhNTk7J5v9r8V7MewHPUqPUCfhDf98Z03ATvXARn+GkDXEUn/AUOWVNUi70jASDN+BxAlfxErvQGOfwApbKQXfgFeLwJCGhTWFGRsYX3ktEiQkYvAmPsziPVvgIB9yOyZiBzXCgPLzFbFRCPJPEMckd3iNRYgI+4CBfUYS5MJtBcOABWIpZmI9NyEQW3Av3ZhRjZCKXiYr5ORDtPwarX87DFZ+CgxuHkQL3YQKaoivco/o4APckDaexB2ZTG8EIZsDgftjBruEx+sM/8v/HoSUsk+EGu2o3uB+ewyxthHz0RRFZvCOLvHAGluIZnN0az8FJWA5jL3ZiCQ7CktWE4Ya7d5apHhJhpsMQiA61pBnMw0A0h6u5jUvIhqvtiHgsRCe4oCmwXTPg52xVs/bMOFlwD2KwDtbfFI1HOIRjsPYesHA44Fgsw2I0wkPsh1m7ULswi8XHlKNW1flhAe7iOt7DNhyBLkiFNf8OS3kE0+FKL8MJpqIQg2Hbuo8+D0VzAnN5SYdl2go7w2wMSzQe22BLOqn9bysbV9Dz52vgAWwGwxNvZSJtuh6v4caZvnePZTNV96MqLIeHzw20CcJZV4EHzqzNxvpXgBnlR1EnJ5kEV+iASRgNN87zsBa26j3YQathzERFeNL9rKu2bXPgAmOpTpol+sYP1tb0FsFO8A97wJJ43zjYSjiAYQeZ1RZshCvfgOFwD/292UZK5MbZenbPcZhmuL+9FtxUV2YGnpMVGALDk24Z26MaLGEd2By/rmtKZVnaIha2Zzv0wUh4BfhHHh7voxtoDc+HneQdZpN4IN3c+1QmeOmFMwjwC7OwW7zoJsLLyoEvwJo6uBnaRWewCm/gniVgN1yQp9/rIxglblPujgLOhTN7Yr1fPP7J8CB5W3pdrIEb6+VoE9imfhm1gM0ykcX6+WCU9Y1mu/qNZu3tEN8dsA28r7xGLJ1ZublGbyQxuNlGotQJDCbxyrWeHWD9PXD2vRM2wz6YqYtJZ2AP2m9R5gQGk3hgwveVV0pl+BVpI3SH+2VD3GACr5L/E0wcE5r8DxEI/AB0rgvmy23CyQAAAABJRU5ErkJggg==';
        $xpath       = get_xpath($webresponse);

        $resultcount = 0;
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

                $resultcount++;
                $url         = $url->textContent;
                $title       = $xpath->evaluate(".//h3", $result)[$x];
                $description = $xpath->evaluate(".//div[contains(@class, 'VwiC3b')]", $result)[$x];
                $image       = $xpath->evaluate(".//img[contains(@class, 'XNo5Ab')]/@src", $result)[$x];
                $sitename    = $xpath->evaluate(".//span[contains(@class, 'VuuXrf')]", $result)[$x];
                $image_data  = $image == null ?
                                ($x > 0 ? $previmage : $blank_image) :
                                $image->textContent;
                $previmage   = $image_data;
                array_push($results,
                    array (
                        "title"       => htmlspecialchars($title->textContent),
                        "sitename"    => $sitename == null ? "" : htmlspecialchars($sitename->textContent),
                        "image"       => $image_data,
                        "url"         => htmlspecialchars($url),
                        "base_url"    => htmlspecialchars(get_base_url($url)),
                        "description" => $description == null ?
                                            "No description was provided for this site." :
                                            htmlspecialchars($description->textContent)
                    )
                );
            }
        }
        $config['result_count'] = $resultcount;
//debug_var("1: result_count = ".$config['result_count']);

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

    static function getVideoResults($search_ch, $query, &$config)
    {
        if (curl_getinfo($search_ch)['http_code'] == '302') {
            //@@@ TODO Try another instance
            echo curl_multi_getcontent($search_ch);
            //die();
        }

        $results     = [];
        $webresponse = curl_multi_getcontent($search_ch);
        //$xpath       = get_xpath($webresponse);

        echo($webresponse);

        return $results;
    }
}