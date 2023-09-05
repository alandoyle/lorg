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
 * User Agent parser class
 * Parse out the User Agent string
 * Cutdown version of https://github.com/guiliredu/simple-user-agent/blob/master/src/UserAgent.php
 *
 **************************************************************************************************/

 class UserAgentParser
 {
    protected $agent;
    protected $os;
    protected $osver;
    protected $browser;
    protected $prefix;
    protected $version;
    protected $engine;
    protected $isMobile = false;

    protected $oss = [
        'Android'       => ['Android'],
        'Linux'         => ['linux', 'Linux'],
        'Mac OS X'      => ['Macintosh', 'Mac OS X'],
        'iOS'           => ['like Mac OS X'],
        'Windows'       => ['Windows NT', 'win32'],
        'Windows Phone' => ['Windows Phone'],
        'Chrome OS'     => ['CrOS'],
    ];
    protected $browsers = [
        'Apple Safari'      => ['Safari'],
        'Google Chrome'     => ['Chrome'],
        'Edge'              => ['Edge'],
        'Internet Explorer' => ['MSIE'],
        'Mozilla Firefox'   => ['Firefox'],
        'Opera'             => ['OPR', 'Opera'],
        'Netscape'          => ['Netscape'],
        'cURL'              => ['curl'],
        'Wget'              => ['Wget'],
    ];
    protected $engines = [
        'Gecko'    => ['Gecko'],
        'Chromium' => ['AppleWebKit'],
        'WebKit'   => ['X) AppleWebKit'],
        'EdgeHTML' => ['Edge'],
        'Trident'  => ['Trident', 'MSIE'],
    ];

    public function __construct($agent = null)
    {
        if (!$agent && isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }

        $this->agent = $agent;
        $this->Parse();
    }

    public function Parse()
    {
        // Find OS
        foreach ($this->oss as $os => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->os    = $os;
                    $this->osver = $this->parseOsVersion($pattern);
                    break;
                }
            }
        }

        // Is it a mobile OS?
        if ($this->os == 'Android' ||
            $this->os == 'iOS' ||
            $this->os == 'Windows Phone') {
                $this->isMobile = true;
        }

        // Find browser
        foreach ($this->browsers as $browser => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->browser = $browser;
                    $this->prefix = $pattern;
                    break;
                }
            }
        }

        // Engine
        foreach ($this->engines as $engine => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->engine = $engine;
                    break;
                }
            }
        }

        // Browser version
        $pattern = '#(?<browser>' . join('|', ['Version', $this->prefix, 'other']) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, $this->agent, $matches);

        $this->version = $matches['version'][0];

        if (count($matches['browser']) != 1) {
            $this->version = strripos($this->agent, "Version") < strripos($this->agent, $this->prefix) ? $matches['version'][0] : $matches['version'][1];
        }
    }

    public function IsMobile()
    {
        return $this->isMobile;
    }

    public function GetInfo()
    {
        return [
            'agent'     => $this->GetAgent(),
            'os'        => $this->GetOS(),
            'osver'     => $this->GetOSVersion(),
            'browser'   => $this->GetBrowser(),
            'engine'    => $this->GetEngine(),
            'prefix'    => $this->GetPrefix(),
            'version'   => $this->GetVersion(),
            'is_mobile' => $this->IsMobile() ? 'true' : 'false',
        ];
    }

    public function GetEngine()
    {
        return $this->engine;
    }

    public function GetAgent()
    {
        return $this->agent;
    }

    public function GetOS()
    {
        return $this->os;
    }

    public function GetOSVersion()
    {
        return $this->osver;
    }

    public function GetBrowser()
    {
        return $this->browser;
    }

    public function GetPrefix()
    {
        return $this->prefix;
    }

    public function GetVersion()
    {
        return $this->version;
    }

    private function parseOsVersion($pattern)
    {
debug_var("Getting '$pattern' OS version.");
        $startpoint = strpos($this->agent, $pattern);
        $length  = strpos($this->agent, ';', $startpoint) - $startpoint;
        $string = substr($this->agent, $startpoint, $length);
/*
        'Android'       => ['Android'],
        'Linux'         => ['linux', 'Linux'],
        'macOS'         => ['Macintosh', 'Mac OS X'],
        'iOS'           => ['like Mac OS X'],
        'Windows'       => ['Windows NT', 'win32'],
        'Windows Phone' => ['Windows Phone'],
        'Chrome OS'     => ['CrOS'],
*/
        switch($pattern)
        {
            case 'Windows NT':
                break;
        }
        return '';
    }
 }