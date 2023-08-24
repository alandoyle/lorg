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
 * This is the special Weather Engine.
 *
 ***************************************************************************************************
 */

class Weather {
    // Static function
    public static function getUrl()
    {
        // Build the URL.
        $remoteip    = get_my_ip_external();
        $url         = "https://wttr.in/@$remoteip?format=j1";

        return $url;
    }

    public static function getResults($special_ch)
    {
        $response = emptyResponse();
        try
        {
            // No data connection found. Wttr.in down?
            if ($special_ch === NULL) {
                return $response;
            }

            // Build the URL and download the data from Wttr.in.
            $webresponse   = curl_multi_getcontent($special_ch);

            // Check if we've got some data.
            if (empty($webresponse)) {
                return $response;
            }

            // Decode the JSON response.
            $json_response = json_decode($webresponse, true);
            if (!$json_response) {
                return $response;
            }

            $current_weather    = $json_response["current_condition"][0];
            $temp_c             = $current_weather["temp_C"];
            $temp_f             = $current_weather["temp_F"];
            $description        = $current_weather["weatherDesc"][0]["value"];
            $formatted_response = "$description - $temp_c °C | $temp_f °F";

            $nearest_area         = $json_response["nearest_area"][0];
            $areaName             = $nearest_area["areaName"][0]["value"];
            $country              = $nearest_area["country"][0]["value"];
            $formatted_sourcename = "Current Weather".((!$areaName&&!$country)?"":" for ")."$areaName".((!$areaName||!$country)?"":",")." $country";


            $response["response"]   = htmlspecialchars($formatted_response);
            $response["source"]     = "https://wttr.in";
            $response["sourcename"] = $formatted_sourcename;
        }
        catch(Exception $e) {}

        return $response;
    }
}