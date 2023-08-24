<?php
/**
 * This file is part of alandoyle/lorg
 *
 * @copyright Copyright (c) Alan Doyle <me@alandoyle.com>
 * @license https://opensource.org/license/agpl-v3/ GNU Affero General Public License version 3
 */

/** Text Search */
const  SEARCH_TEXT  = 0;
/** Image Search */
const  SEARCH_IMAGE = 1;
/** Video Search */
const  SEARCH_VIDEO = 2;

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


function get_base_url($url)
{
    $split_url = explode("/", $url);
    $base_url = $split_url[0] . "//" . $split_url[2] . "/";
    return $base_url;
}

function get_xpath($response)
{
    $htmlDom = new DOMDocument;
    @$htmlDom->loadHTML($response);
    $xpath = new DOMXPath($htmlDom);

    return $xpath;
}

function get_ua($use_random)
{
    $olduseragents = array(
        'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.80 Safari/537.36',
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36 OPR/36.0.2130.80',
        'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko',
        'Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0',
        'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)',
    );

    $newuseragents = array(
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
    );

    return $use_random ?
            $olduseragents[array_rand($olduseragents, 1)] :
            $newuseragents[array_rand($newuseragents, 1)];
}

function getCurlOptions()
{
    return array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "UTF-8",
        CURLOPT_HTTPHEADER => ['Accept: */*'],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_WHATEVER,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 18,
        CURLOPT_VERBOSE => false
    );
}

function download_url($url, $use_random = false)
{
    $finfo = new finfo(FILEINFO_MIME);
    $ua = get_ua($use_random);
    $ch = curl_init($url);
    curl_setopt_array($ch, getCurlOptions());
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    $filedata = curl_exec($ch);
    $filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $filetype = $finfo->buffer($filedata);

    return [
        'data' => $filedata,
        'size' => $filesize,
        'type' => $filetype
    ];
}

function emptyResponse()
{
    return [
        "response"   => null,
        "source"     => null,
        "sourcename" => null,
        "image_url"  => null
    ];
}

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

function is_valid_ip_address($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

function get_my_ip_external()
{
    $icanhazip = "https://ipv4.icanhazip.com";
    $remotedetails = download_url($icanhazip);

    if (!array_key_exists("data", $remotedetails)) {
        $remotedetails['data'] = '';
    }
    return trim($remotedetails['data']);
}

function debug_var($var)
{
    echo "<pre>$var</pre>";
}

function debug_array($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
