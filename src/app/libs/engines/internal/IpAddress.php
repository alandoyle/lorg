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
 * This is the special External IP Address Engine.
 *
 ***************************************************************************************************
 */

class IpAddress {
   // Static function
    /**
     * Get External IP address.
     * @return array()
     */
    public static function getResults()
    {
        $response = emptyResponse();

        try
        {
            $ipAddress = '';
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
                $ipAddress = get_my_ip_external();
            }

            // Set the response
            $response["response"]   = $ipAddress;
            $response["sourcename"] = "My External IP";
        }
        catch (Exception $e) {}

        return $response;
    }
}