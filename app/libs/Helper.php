<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 *
 ***************************************************************************************************
 *
 * Helper functions used within lorg.
 *
 **************************************************************************************************/

 /**
 * Get the file type for the specified filename.
 *
 * @param string $filename
 * @return string
 */
function file_get_type($filename)
{
    // Best guess based on file extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    switch ($extension)
    {
    case 'jpg':
    case 'jpeg': $filetype = "image/jpeg";    break;
    case 'png':  $filetype = "image/png";     break;
    case 'gif':  $filetype = "image/gif";     break;
    case 'svg':  $filetype = "image/svg+xml"; break;
    case 'ico':  $filetype = "image/x-icon";  break;
    case 'css':  $filetype = "text/css";      break;
    default:     $filetype = "text/plain";    break;
    }

    return $filetype;
}

/**
 * Get the base URL from the full URL passed.
 *
 * @param string $url
 * @return string
 */
function get_base_url($url)
{
    $split_url = explode("/", $url);
    $base_url = $split_url[0] . "//" . $split_url[2] . "/";
    return $base_url;
}

/**
 * Convert the text response into a DOMXPath object.
 *
 * @param string $response
 * @return DOMXPath
 */
function get_xpath($response)
{
    $htmlDom = new DOMDocument;
    @$htmlDom->loadHTML($response);
    $xpath = new DOMXPath($htmlDom);

    return $xpath;
}

/**
 * Generates an Image Proxy URL for agiven image (if enabled).
 *
 * @param string $imageurl
 * @param array $config
 * @return string
 */
function get_image_url($imageurl, $config)
{
    return (($config['use_image_proxy'] === true) ?
                $config['base_url']."/imageproxy?url=".urlencode($imageurl) :
                $imageurl);
}

/**
 * Get a random UserAgent.
 *
 * @param none
 * @return string
 */
function get_random_ua()
{
    $useragents = array(
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:99.0) Gecko/20100101 Firefox/99.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.79 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:101.0) Gecko/20100101 Firefox/101.0',
    );
    return $useragents[array_rand($useragents, 1)];
}

/**
 * Get the client hints.
 *
 * @param string $ua
 * @return array
 */
function get_client_hints($ua)
{
    $hints  = [];
    $parser = new UserAgentParser($ua);

    if ($parser->GetPrefix() != 'Firefox') {
        $os      = $parser->GetOS();
        $osver   = $parser->GetOSVersion();
        $version = $parser->GetVersion();
        $model   = $parser->GetModel();
        $browser = $parser->GetBrowser();
        $engine  = $parser->GetEngine();
        $mobile  = $parser->IsMobile() === true ? '1' : '0';

        $count = 0;
        if ($browser != '') {
            // Add Sec-Ch-Ua
            if (!empty($version)) { $hints[$count++] =  "Sec-Ch-Ua: \"$engine\";v=\"$version\", \"Not)A;Brand\";v=\"$version\", \"$browser\";v=\"$version\""; }
            // Add Sec-Ch-Ua-Mobile
            $hints[$count++] =  "Sec-Ch-Ua-Mobile: ?$mobile";
            // Add Sec-Ch-Ua-Model
            if (!empty($model))   { $hints[$count++] = "Sec-Ch-Ua-Model: $model"; }
            // Add Sec-Ch-Ua-Platform
            if (!empty($os))      { $hints[$count++] = "Sec-Ch-Ua-Platform: $os"; }
            // Add Sec-Ch-Ua-Platform-Version
            if (!empty($osver))   { $hints[$count++] = "Sec-Ch-Ua-Platform-Version: $osver"; }
        }
    }

    return $hints;
}

/**
 * Get the CURL options.
 *
 * @param string $ua
 * @param string $accept_langauge
 * @return array
 */
function get_curl_options($ua, $accept_langauge = '')
{
    if (empty($accept_langauge)) {
        $accept_langauge = 'en-US';
    }

    $headers = [
        'Accept: */*',
        "Accept-Language: $accept_langauge,en;q=0.9",
        'Dnt: 1',
        'Pragma: no-cache',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
    ];

    $client_hints = get_client_hints($ua);
    $hint_count   = count($client_hints);
    if ($hint_count > 0) {
        $current_hint = 0;
        while ($current_hint < $hint_count) {
            $headers[$hint_count + $current_hint] = $client_hints[$current_hint++];
        }
    }

    return array(
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => "UTF-8",
        CURLOPT_HTTPHEADER      => $headers,
        CURLOPT_IPRESOLVE       => CURL_IPRESOLVE_WHATEVER,
        CURLOPT_CUSTOMREQUEST   => "GET",
        CURLOPT_PROTOCOLS       => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        CURLOPT_MAXREDIRS       => 5,
        CURLOPT_TIMEOUT         => 18,
        CURLOPT_VERBOSE         => false
    );
}

/**
 * Download a file from a remote host.
 *
 * @param string $url
 * @param string $ua
 * @return array
 */
function download_url($url, $ua = '')
{
    // Make sure we have a User Agent
    if ($ua == '') {
        $ua = get_random_ua();
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, get_curl_options($ua));
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);

    $finfo = new finfo(FILEINFO_MIME);
    $filedata = curl_exec($ch);
    $filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $filetype = $finfo->buffer($filedata);

    return [
        'data' => $filedata,
        'size' => $filesize,
        'type' => $filetype
    ];
}

/**
 * Get an empty response array.
 *
 * @param none
 * @return array
 */
function emptyResponse()
{
    return [
        "response"   => null,
        "source"     => null,
        "source_url" => null,
        "sourcename" => null,
        "image_url"  => null
    ];
}

/**
 * Add ellipsis (...) to the end of a text block.
 *
 * @param string $text
 * @return string
 */
function addEllipsis($text)
{
    $responsetext = $text;

    // Remove trailing \n
    if (substr($responsetext, -2, 2) == '\n') {
        $responsetext = substr($responsetext, 0, strlen($responsetext)-2);
    }

    // Remove any trailing .
    if (substr($responsetext, -1) == '.') {
        $responsetext = substr($responsetext, 0, strlen($responsetext)-1);
    }

    // Add ellipses
    $responsetext .= '…';

    return $responsetext;
}

/**
 * Determines if given IP address is a valid IPV4 or IPV6 address.
 *
 * @param string $ip
 * @return bool
 */
function is_valid_ip_address($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

/**
 * Determines if external IP address of the user.
 *
 * @param none
 * @return string
 */
function get_my_ip_external()
{
    $ipAddress = '';

    try
    {
        if (! empty($_SERVER['HTTP_CLIENT_IP']) && is_valid_ip_address($_SERVER['HTTP_CLIENT_IP'])) {
            // check for shared ISP IP
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check for IPs passing through proxy servers
            // check if multiple IP addresses are set and take the first one
            $ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipAddressList as $ip) {
                if (is_valid_ip_address($ip)) {
                    $ipAddress = $ip;
                    break;
                }
            }
        }

        // Another check in case we only have Private IP's in $_SERVER['HTTP_X_FORWARDED_FOR']
        if (!empty(trim($ipAddress))) {
            ; // Nothing to do :)
        } else if (! empty($_SERVER['HTTP_X_FORWARDED']) && is_valid_ip_address($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (! empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && is_valid_ip_address($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } else if (! empty($_SERVER['HTTP_FORWARDED_FOR']) && is_valid_ip_address($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (! empty($_SERVER['HTTP_FORWARDED']) && is_valid_ip_address($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else if (! empty($_SERVER['REMOTE_ADDR']) && is_valid_ip_address($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $icanhazip = "https://ipv4.icanhazip.com";
            $remotedetails = download_url($icanhazip);

            if (!array_key_exists("data", $remotedetails)) {
                $remotedetails['data'] = '';
            }
            $ipAddress =  trim($remotedetails['data']);
        }
    }
    catch(Exception $e) {
        $ipaddress = 'UNKNOWN';
    }

    return $ipAddress;
}

/**
 * Determines if external IP address details.
 *
 * @param none
 * @return array
 */
function get_my_ip_details()
{
    $download_file = true;
    $ipaddress     = get_my_ip_external();
    $ip_api_file   = "/etc/lorg/cache/ip/$ipaddress.json";

    // Check if directory exists
    if (is_dir('/etc/lorg/cache/ip') === false) {
        mkdir('/etc/lorg/cache/ip', 0644, true);
    }

    // Check if cache file is less than 2 hours old.
    if ((file_exists($ip_api_file) === true) &&
        (time() - filemtime($ip_api_file) < TWO_HOURS)) {
        $download_file = false;
    }

    // Download a new copy of the JSON file.
    if ($download_file) {
        $ip_api_url = "http://ip-api.com/json/$ipaddress?fields=57562";
        $response = download_url($ip_api_url);
        file_put_contents($ip_api_file, $response['data']);
    }

    // Return the contents of the file.
    $contents = file_get_contents($ip_api_file);
    return json_decode($contents, true);
}

/**
 * Download the weather details.
 *
 * @param array $ipdetails
 * @return string
 */
function get_weather_data($ipdetails)
{
    if (is_array($ipdetails) === false) {
        return "";
    }

    if (array_key_exists('lat', $ipdetails) === false) {	
        return "";
    }
    if (array_key_exists('lon', $ipdetails) === false) {
        return "";
    }

    $download_file = true;
    $latitude      = $ipdetails['lat'];
    $longitude     = $ipdetails['lon'];
    $country       = urlencode($ipdetails['countryCode']); // "GB"
    $region        = urlencode($ipdetails['regionName']);  // "England"
    $city          = urlencode($ipdetails['city']);        // "Birmingham"
    $cache_file    = "/etc/lorg/cache/region/$country-$region-$city.json";

    // Check if directory exists
    if (is_dir('/etc/lorg/cache/region') === false) {
        mkdir('/etc/lorg/cache/region', 0644, true);
    }

    // Check if cache file is less than 1 hour old.
    if ((file_exists($cache_file) === true) &&
        (time() - filemtime($cache_file) < ONE_HOUR)) {
        $download_file = false;
    }

    // Download a new copy of the JSON file.
    if ($download_file) {
        $weather_url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current=temperature_2m,apparent_temperature,precipitation,cloudcover&windspeed_unit=mph&timezone=UTC";
        $response = download_url($weather_url);
        file_put_contents($cache_file, $response['data']);
    }

    // Return the contents of the file.
    return file_get_contents($cache_file);
}

/**
 * Returns a GUIDv4 string
 *
 * Uses the best cryptographically secure method
 * for all supported pltforms with fallback to an older,
 * less secure version.
 *
 * @param bool $trim
 * @return string
 */
function GenGUIDv4 ($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace.
              substr($charid,  0,  8).$hyphen.
              substr($charid,  8,  4).$hyphen.
              substr($charid, 12,  4).$hyphen.
              substr($charid, 16,  4).$hyphen.
              substr($charid, 20, 12).
              $rbrace;
    return $guidv4;
}

/**
 * Outputs a blank image.
 *
 * @param none
 * @return string
 */
function get_blank_image()
{
    return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwABGUAAARlAAYDjddQAAAOSSURBVEhLrdV5aM5xHMDxZ3PMMVf+cISZ3CY5IkeO3CIUJolyH5ErOfOHoyaEyJE/kKNNliNalMQi1x9irs3E3LTYjG3O9/vxPI+WLZRPvfx+m2ff7/fz+X6+3yfwt5GYOKZ86PWfIir0LDUYtC6PbriFDxiKGkhNTk7J5v9r8V7MewHPUqPUCfhDf98Z03ATvXARn+GkDXEUn/AUOWVNUi70jASDN+BxAlfxErvQGOfwApbKQXfgFeLwJCGhTWFGRsYX3ktEiQkYvAmPsziPVvgIB9yOyZiBzXCgPLzFbFRCPJPEMckd3iNRYgI+4CBfUYS5MJtBcOABWIpZmI9NyEQW3Av3ZhRjZCKXiYr5ORDtPwarX87DFZ+CgxuHkQL3YQKaoivco/o4APckDaexB2ZTG8EIZsDgftjBruEx+sM/8v/HoSUsk+EGu2o3uB+ewyxthHz0RRFZvCOLvHAGluIZnN0az8FJWA5jL3ZiCQ7CktWE4Ya7d5apHhJhpsMQiA61pBnMw0A0h6u5jUvIhqvtiHgsRCe4oCmwXTPg52xVs/bMOFlwD2KwDtbfFI1HOIRjsPYesHA44Fgsw2I0wkPsh1m7ULswi8XHlKNW1flhAe7iOt7DNhyBLkiFNf8OS3kE0+FKL8MJpqIQg2Hbuo8+D0VzAnN5SYdl2go7w2wMSzQe22BLOqn9bysbV9Dz52vgAWwGwxNvZSJtuh6v4caZvnePZTNV96MqLIeHzw20CcJZV4EHzqzNxvpXgBnlR1EnJ5kEV+iASRgNN87zsBa26j3YQathzERFeNL9rKu2bXPgAmOpTpol+sYP1tb0FsFO8A97wJJ43zjYSjiAYQeZ1RZshCvfgOFwD/292UZK5MbZenbPcZhmuL+9FtxUV2YGnpMVGALDk24Z26MaLGEd2By/rmtKZVnaIha2Zzv0wUh4BfhHHh7voxtoDc+HneQdZpN4IN3c+1QmeOmFMwjwC7OwW7zoJsLLyoEvwJo6uBnaRWewCm/gniVgN1yQp9/rIxglblPujgLOhTN7Yr1fPP7J8CB5W3pdrIEb6+VoE9imfhm1gM0ykcX6+WCU9Y1mu/qNZu3tEN8dsA28r7xGLJ1ZublGbyQxuNlGotQJDCbxyrWeHWD9PXD2vRM2wz6YqYtJZ2AP2m9R5gQGk3hgwveVV0pl+BVpI3SH+2VD3GACr5L/E0wcE5r8DxEI/AB0rgvmy23CyQAAAABJRU5ErkJggg==';
}

/**
 * Outputs a variable for debugging purposes.
 *
 * @param string $var
 * @return none
 */
function debug_var($var)
{
    echo "<pre>$var</pre>\n";
}

/**
 * Outputs the contents of an array for debugging purposes.
 *
 * @param array $array
 * @return none
 */
function debug_array($array)
{
    if (is_array($array) === false) {
        debug_var("INVALID ARRAY!");
    }

    /*******************************************************************************************
     * Sort the array for ease of readability.
     ******************************************************************************************/
    ksort($array, SORT_NATURAL);

    /*******************************************************************************************
     * Output the sorted array.
     ******************************************************************************************/
    echo "<pre>";
    print_r($array);
    echo "</pre>\n";
}