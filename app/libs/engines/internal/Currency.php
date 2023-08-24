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
 * This is the special Currency Engine.
 *
 ***************************************************************************************************
 */

class Currency {
    // Static function
    public static function getUrl()
    {
        // Build the URL.
        $url = "https://cdn.moneyconvert.net/api/latest.json";

        return $url;
    }

    public static function getResults($special_ch, $query)
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

            $split_query         = explode(" ", $query);
            $base_currency       = strtoupper($split_query[1]);
            $currency_to_convert = strtoupper($split_query[3]);
            $amount_to_convert   = floatval($split_query[0]);

            // Decode the JSON response.
            $json_response = json_decode($webresponse, true);
            if (!$json_response) {
                return $response;
            }

            $rates =  $json_response["rates"];
            if (array_key_exists($base_currency, $rates) && array_key_exists($currency_to_convert, $rates)) {
                $base_currency_response       = $rates[$base_currency];
                $currency_to_convert_response = $rates[$currency_to_convert];
                $conversion_result            = ($currency_to_convert_response / $base_currency_response) * $amount_to_convert;
                $formatted_response           = "$amount_to_convert $base_currency = $conversion_result $currency_to_convert";

                $response["response"]   = htmlspecialchars($formatted_response);
                $response["sourcename"] = "Currency Conversion";
                $response["source"]     = "https://moneyconvert.net/";
            }
        }
        catch (Exception $e) {}

        return $response;
    }
}