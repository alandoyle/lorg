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
 * This is the specialUser Agent Engine.
 *
 ***************************************************************************************************
 */

class UserAgent {
   // Static function
    /**
     * Get User Agent.
     * @return array()
     */
    public static function getResults()
    {
        $response = emptyResponse();

        try
        {
            $response["response"]   = $_SERVER["HTTP_USER_AGENT"];
            $response["sourcename"] = "My User Agent";
        }
        catch (Exception $e) {}

        return $response;
    }
}