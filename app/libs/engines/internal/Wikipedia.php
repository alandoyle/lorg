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
 * This is the special Wikipedia Engine.
 *
 ***************************************************************************************************
 */

class Wikipedia {
    // Static function
    public static function getUrl($query, $config)
    {
        $query_encoded = urlencode($query);

        // Build the URL.
        $language = isset($_COOKIE["wikipedia_language"]) ? trim(htmlspecialchars($_COOKIE["wikipedia_language"])) : $config['wikipedia_language'];
        $url      = "https://$language.wikipedia.org/w/api.php?format=json&action=query&prop=extracts%7Cpageimages&exintro&explaintext&redirects=1&pithumbsize=500&titles=$query_encoded";

        return $url;
    }

    public static function getResults($special_ch, $query, $config)
    {
        $response = emptyResponse();
        try
        {
            $language = isset($_COOKIE["wikipedia_language"]) ? trim(htmlspecialchars($_COOKIE["wikipedia_language"])) : $config['wikipedia_language'];

            // No data connection found. Wikipedia down?
            if ($special_ch === NULL) {
                return $response;
            }

            // Build the URL and download the data from Wikipedia.
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

            $first_page = array_values($json_response["query"]["pages"])[0];
            if (!array_key_exists("missing", $first_page)) {
                $ncount = 0;
                // Read the details from the JSON response.
                $description  = explode("\n", $first_page["extract"]);
                $responsetext = '';

                // Add a decent description.
                while ((strlen($responsetext) < 500) && ($ncount < count($description))) {
                    $responsetext .= $description[$ncount].'\n';
                    $ncount++;
                }

                // Add an ellipsis
                $responsetext = addEllipsis($responsetext);

                $response["source"]     = "https://$language.wikipedia.org/wiki/$query";
                $response["sourcename"] = "Wikipedia";
                $response["response"]   = htmlspecialchars($responsetext);

                // See if there's a thumbnail available.
                if (array_key_exists("thumbnail",  $first_page)) {
                    $response["image_url"] = get_image_url($first_page["thumbnail"]["source"], $config);
                }
            }
        }
        catch (Exception $e) {}

        return $response;
    }
}