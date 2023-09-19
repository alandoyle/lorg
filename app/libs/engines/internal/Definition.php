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
 * This is the special Definition Engine.
 *
 ***************************************************************************************************
 */

class Definition {
    // Static function
    public static function getUrl($query)
    {
        // Build the URL.
        $split_query      = explode(" ", $query);
        $reversed_split_q = array_reverse($split_query);
        $word_to_define   = $reversed_split_q[1];
        $url              = "https://api.dictionaryapi.dev/api/v2/entries/en/$word_to_define";

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

            if (!array_key_exists("title", $json_response)) {
                // Read the details from the JSON response.
                $response["response"]   = htmlspecialchars($json_response[0]["meanings"][0]["definitions"][0]["definition"]);
                $response["source"]     = "https://dictionaryapi.dev";
                $response["source_url"] = htmlspecialchars($json_response[0]["sourceUrls"][0]);
                $response["sourcename"] = ucfirst($json_response[0]["word"]);
            }
        }
        catch(Exception $e) {}

        return $response;
    }
}