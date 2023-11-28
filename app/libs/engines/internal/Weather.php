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
    public static function getResults($timezone)
    {
        $response = emptyResponse();
        try
        {
            // Build the URL.
            $ipdetails   = get_my_ip_details();
            $webresponse = get_weather_data($ipdetails);
            // Check if we've got some data.
            if (empty($webresponse)) {
                return $response;
            }

            // Decode the JSON response.
            $json_response = json_decode($webresponse, true);
            if (!$json_response) {
                return $response;
            }

            $weather_details = array("temperature_2m"       => "Temperature",
                                     "apparent_temperature" => "Feels like",
                                     "precipitation"        => "Precipitation",
                                     "cloudcover"           => "Cloud Cover",
                                    );
            $countrycode     = $ipdetails['countryCode']; // e.g. "GB"
            $country         = $ipdetails['regionName'];  // e.g. "England"
            $areaName        = $ipdetails['city'];        // e.g. "Birmingham"
            $current_weather = $json_response["current"];
            $current_units   = $json_response["current_units"];

            $update_time = $current_weather['time'];

            $datetime = $update_time;
            $date = new \DateTime( $datetime, new \DateTimeZone( 'UTC' ) );
            $date->setTimezone( new \DateTimeZone( $timezone ) );
            $update_time = $date->format('D, d M Y H:i:s');

            $formatted_sourcename = "Current Weather".((!$areaName&&!$country)?"":" for ")."$areaName".((!$areaName||!$country)?"":",")." $country";

            $weather_forecast = "<div class='weather-container'><strong>Updated: $update_time</strong><ul class='weather-table'>";
            foreach($weather_details as $key => $name) {
                $value = $current_weather[$key];
                $units = $current_units[$key];
                $weather_forecast .= "<li class='weather-row'><div class='col col-1'>$name:</div><div class='col col-2'>$value $units</div></li>";
            }
            $weather_forecast .= "</ul></div>";

            $response["response"]   = $weather_forecast;
            $response["source"]     = "https://open-meteo.com";
            $response["source_url"] = "https://open-meteo.com/en/docs";
            $response["sourcename"] = $formatted_sourcename;
        }
        catch(Exception $e)
        {
            $response = "";
        }

        return $response;
    }
}