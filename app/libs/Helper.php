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
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36",
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:99.0) Gecko/20100101 Firefox/99.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.79 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:101.0) Gecko/20100101 Firefox/101.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
    );
//@@@ Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36
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
debug_var("Parsing '$ua'...");

    $hints = [];

    $parser = new UserAgentParser($ua);
debug_array($parser->GetInfo());

    if ($parser->GetPrefix() != 'Firefox') {
        $os      = $parser->GetOS();
        $osver   = ''; //$parser->GetOSVersion();
        $version = $parser->GetVersion();
        $browser = $parser->GetBrowser();
        $engine  = $parser->GetEngine();
        $mobile  = $parser->IsMobile() === true ? '1' : '0';

        $sec_ch_au =
        $sec_ch_au_mobile =
        $sec_ch_au_platform =
        $sec_ch_au_platform_version = '';

        if ($browser != '') {
            // Add Sec-Ch-Ua
            $sec_ch_au = "Sec-Ch-Ua: \"$engine\";v=\"$version\", \"Not)A;Brand\";v=\"$version\", \"$browser\";v=\"$version\"";
            // Add Sec-Ch-Ua-Mobile
            $sec_ch_au_mobile = "Sec-Ch-Ua-Mobile: ?$mobile";
            // Add Sec-Ch-Ua-Platform
            $sec_ch_au_platform = "Sec-Ch-Ua-Platform: $os";
            // Add Sec-Ch-Ua-Platform-Version
            $sec_ch_au_platform_version = "Sec-Ch-Ua-Platform-Version: $osver";
        }
        $hints = [
            $sec_ch_au,
            $sec_ch_au_mobile,
            'Sec-Ch-Ua-Model:',
            $sec_ch_au_platform,
            $sec_ch_au_platform_version,
        ];
    }

    // Remove empty array elements
    $hints = (array_filter($hints, fn($value) => !is_null($value) && $value !== ''));
    return $hints;
}

/**
 * Get the CURL options.
 *
 * @param string $ua
 * @return array
 */
function get_curl_options($ua, $accept_langauge)
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
    $hint_count = count($client_hints);

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
    if (substr($responsetext, -1) === '.') {
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
    echo "<pre>";
    print_r($array);
    echo "</pre>\n";
}

/**
 * Convert an object to an array.
 *
 * @param array $array
 * @return array
 */
function convert_to_array($array) {

    if (is_object($array)) {
        $array = get_object_vars($array);
    }

    if (is_array($array)) {
        return array_map(null, $array);
    }

    return $array;
}