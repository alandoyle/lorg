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
        $ua                = $config['ua'];

        // Generate arc id (Use updated Google search endpoint via unixfox's research for SearXNG)
        if ($config['arc_timestamp'] + 3600 < time()) {
            $charset = "01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-";
            $config['arc_id'] = "srp_";

            for ($i = 0; $i < 24; $i++) {
                $c = random_int(0, strlen($charset) - 1);
                $config['arc_id'] .= $charset[$c];
            }

            $config['arc_id'] .= "_1";
            $config['arc_timestamp'] = time();
        }

        // Start building the URL.
        $url = "https://www.google.$domain/search?q=$query_encoded&nfpr=1";

        switch($type)
        {
            case SEARCH_IMAGE: // Image Search
                $url .= "&oq=$query_encoded&udm=2";
                $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20120101 Firefox/33.0';
                break;
            case SEARCH_TEXT: // Text Search
                $arc_page = sprintf("%02d", $pagenum * 10);
                $url .= "&asearch=arc&async=arc_id:".$config['arc_id'].$arc_page.",use_ac:true,_fmt:html";
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

        $startnum = $pagenum * 10;
        $url .= "&start=$startnum";

        // Save the URL
        $config['search_url'] = $url;

        $google_ch = curl_init($url);
        curl_setopt_array($google_ch, get_curl_options($ua, $config['accept_langauge']));
        curl_setopt($google_ch, CURLOPT_USERAGENT, $ua);
        curl_multi_add_handle($mh, $google_ch);

        return $google_ch;
    }

    static function getEngineName()
    {
        return "Google";
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

        $didyoumean = $xpath->query(".//p[@class='QRYxYe NNMgCf']/a")[0];
        if (!is_null($didyoumean)) {
            $url = $config['base_url'] . "/search?q=" . urlencode(utf8_decode($didyoumean->textContent));
            array_push($results,
                array (
                    "title"       => "Did you mean '".htmlspecialchars(utf8_decode($didyoumean->textContent))."'?",
                    "sitename"    => $config['opensearch_title'],
                    "image"       => get_blank_image(),
                    "url"         => $url,
                    "base_url"    => $config['base_url'],
                    "description" => "",
                    "target"      => ""
                )
            );
            $resultcount++;
        }

        foreach($xpath->query("//div[@class='MjjYud']") as $result) {
            $classcount = count($xpath->evaluate(".//div[@class='yuRUbf']//a/@href", $result));
            if ($classcount == 0) {
                continue;
            }

            for ($x = 0; $x<$classcount; $x++) {
                // Reset prev image
                $previmage = ($x == 0) ? '' : $previmage;

                $url = $xpath->evaluate(".//a[@class='zReHs']/@href", $result)[$x];
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
                            "title"       => htmlspecialchars(utf8_decode($title)),
                            "sitename"    => $sitename == null ? "" : preg_replace('/[^\x20-\x7E]/',' ', $sitename->textContent),
                            "image"       => $image_data,
                            "url"         => htmlspecialchars($url),
                            "base_url"    => htmlspecialchars(get_base_url($url)),
                            "description" => $description == null ?
                                                "No description was provided for this site." :
                                                htmlspecialchars(utf8_decode($description->textContent)),
                            "target"      => "_blank"
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
        foreach($xpath->query("//td[@class='e3goi']") as $result) {
            $url = $xpath->evaluate(".//table[@class='RntSmf']//tr//td//a//@href", $result);
            if ($url == null) {
                continue;
            }

            $title       = $xpath->evaluate(".//span[contains(@class, 'x3G5ab')]//span[contains(@class, 'fYyStc')]", $result);
            $sitename    = $xpath->evaluate(".//span[contains(@class, 'F9iS2e')]//span[contains(@class, 'fYyStc')]", $result);
            $thumbnail   = $xpath->evaluate(".//img[contains(@class, 'DS1iW')]/@src", $result);

            if (!empty($title[0])) {
                $title = trim($title[0]->textContent);
            }
            if (!empty($sitename[0])) {
                $sitename = trim($sitename[0]->textContent);
            }
            if (!empty($thumbnail[0])) {
                $thumbnail = trim($thumbnail[0]->textContent);
            }
            if (!empty($url[0])) {
                $querystring = str_replace("/url?", "", trim($url[0]->textContent));
                $params = [];
                /*******************************************************************************************
                 * Build args
                 ******************************************************************************************/
                $queryarray = explode('&',html_entity_decode($querystring));
                foreach ($queryarray as $value) {
                    $newarg = explode('=', $value);
                    if (count($newarg) === 2) {
                        $params[$newarg[0]] = urldecode($newarg[1]);
                    }
                }
                $url = $params['url'];
            }
            array_push($results,
                array (
                    "title"     => htmlspecialchars($title),
                    "sitename"  => htmlspecialchars($sitename),
                    "thumbnail" => get_image_url($thumbnail, $config),
                    "url"       => $url
                )
            );
            $resultcount++;
        }
        $config['result_count'] = $resultcount;

        return $results;
    }
}