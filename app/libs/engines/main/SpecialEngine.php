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
 * This is the Special Search Engine.
 *
 ***************************************************************************************************
 */

 class SpecialEngine {
    const  SPECIAL_NOTHING    = 0;
    const  SPECIAL_CURRENCY   = 1;
    const  SPECIAL_DEFINITION = 2;
    const  SPECIAL_IPADDRESS  = 3;
    const  SPECIAL_USERAGENT  = 4;
    const  SPECIAL_WEATHER    = 5;
    const  SPECIAL_WIKIPEDIA  = 6;

    static function Init($mh, $query, $type, $pagenum, $config)
    {
        $special_ch  = NULL;
        $specialType = self::checkQuery($query, $type, $pagenum);
        switch ($specialType)
        {
            case self::SPECIAL_CURRENCY:    $url = Currency::getUrl();                 break;
            case self::SPECIAL_DEFINITION:  $url = Definition::getUrl($query);         break;
            case self::SPECIAL_WEATHER:     $url = Weather::getUrl();                  break;
            case self::SPECIAL_WIKIPEDIA:   $url = Wikipedia::getUrl($query, $config); break;
            default:                        $url = NULL;                               break;
        }

        if ($url != NULL) {
            $special_ch = curl_init($url);
            curl_setopt_array($special_ch, get_curl_options($config['ua'], $config['accept_langauge']));
            curl_setopt($special_ch, CURLOPT_USERAGENT, $config['ua']);
            curl_multi_add_handle($mh, $special_ch);
        }

        return $special_ch;
    }

    static function GetResults($special_ch, $query, $type, $pagenum, $config)
    {
        switch (self::checkQuery($query, $type, $pagenum))
        {
            case self::SPECIAL_CURRENCY:    return Currency::getResults($special_ch, $query);
            case self::SPECIAL_DEFINITION:  return Definition::getResults($special_ch);
            case self::SPECIAL_IPADDRESS:   return IpAddress::getResults();
            case self::SPECIAL_USERAGENT:   return UserAgent::getResults();
            case self::SPECIAL_WEATHER:     return Weather::getResults($special_ch);
            case self::SPECIAL_WIKIPEDIA:   return Wikipedia::getResults($special_ch, $query, $config);
            default:                        return [];
        }
    }

    static function checkQuery($query, $type, $pagenum)
    {
        $query_type = self::SPECIAL_NOTHING;

        // Check if we're a text search on page one.
        // Also check if Special results are disabled in the local settings.
        if (($type != SEARCH_TEXT) || ($pagenum != 0) ||
            isset($_COOKIE["disable_special"])) {
            return $query_type;
        }

        $query_lower = strtolower($query);
        $split_query = explode(" ", $query);

        if (strpos($query_lower, "to") &&
            count($split_query) >= 4) {
            $amount_to_convert = floatval($split_query[0]);
            if ($amount_to_convert != 0) {
                // Currency
                $query_type = self::SPECIAL_CURRENCY;
            }
        } else if ((strpos($query_lower, "mean")) ||
                   (strpos($query_lower, "definition")) &&
                   count($split_query) >= 2) {
            // Definition
            $query_type = self::SPECIAL_DEFINITION;
        } else if (strpos($query_lower, "my") !== false) {
            if (strpos($query_lower, "ip")) {
                // Ip Address
                $query_type = self::SPECIAL_IPADDRESS;
            } else if (strpos($query_lower, "user agent") ||
                       strpos($query_lower, "ua")) {
                // User Agent
                $query_type = self::SPECIAL_USERAGENT;
            }
        } else if (strpos($query_lower, "weather") !== false) {
            // Weather
            $query_type = self::SPECIAL_WEATHER;
        } else if (3 > count(explode(" ", $query))) {
            // Wikipedia
            $query_type = self::SPECIAL_WIKIPEDIA;
        }

        // Nothing special
        return $query_type;
    }
}