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
    protected $agent    = '';
    protected $os       = '';
    protected $osver    = '';
    protected $model    = '';
    protected $browser  = '';
    protected $prefix   = '';
    protected $version  = '';
    protected $engine   = '';
    protected $isMobile = false;

    protected $oss = [
        'PlayStation'   => ['PlayStation'],
        'Xbox'          => ['Xbox','XBOX'],
        'Nintendo'      => ['Nintendo'],
        'Windows Phone' => ['Windows Phone'],
        'Android'       => ['Android'],
        'Linux'         => ['linux', 'Linux'],
        'iOS'           => ['like Mac OS X'],
        'macOS'         => ['Macintosh', 'Mac OS X'],
        'Windows'       => ['Windows NT', 'win32'],
        'Chrome OS'     => ['CrOS'],
    ];
    protected $browsers = [
        'Kindle'            => ['Kindle'],
        'Edge'              => ['Edge'],
        'Google Chrome'     => ['Chrome', 'CriOS'],
        'Mozilla Firefox'   => ['Firefox','FxiOS'],
        'PlayStation'       => ['PlayStation'],
        'Apple Safari'      => ['Safari'],
        'Internet Explorer' => ['MSIE'],
        'Opera'             => ['OPR', 'Opera'],
        'Netscape'          => ['Netscape'],
        'cURL'              => ['curl'],
        'Wget'              => ['Wget'],
        'Nintendo Browser'  => ['NintendoBrowser'],
    ];
    protected $engines = [
        'WebKit'   => ['X) AppleWebKit'],
        'Chromium' => ['AppleWebKit'],
        'EdgeHTML' => ['Edge'],
        'Trident'  => ['Trident', 'MSIE'],
        'Gecko'    => ['Gecko'],
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
                if ((strpos($this->agent, $pattern) !== false) &&
                    (empty($this->os) === true)) {
                    $this->parseOsDetails($os);
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
                if ((strpos($this->agent, $pattern) !== false) &&
                    (empty($this->browser) === true)) {
                    $this->browser = $browser;
                    $this->prefix  = $pattern;
                    break;
                }
            }
        }

        // Engine
        foreach ($this->engines as $engine => $patterns) {
            foreach ($patterns as $pattern) {
                if ((strpos($this->agent, $pattern) !== false) &&
                    (empty($this->engine) === true)) {
                    $this->engine = $engine;
                    break;
                }
            }
        }

        // Browser version
        $pattern = '#(?<browser>' . join('|', ['Version', $this->prefix, 'other']) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, $this->agent, $matches);
        $version = $matches['version'][0];
        $parts = explode('.', $version);
        $this->version = is_array($parts) ? $parts[0].'.0' : '';

        if (count($matches['browser']) != 1) {
            $version = strripos($this->agent, "Version") < strripos($this->agent, $this->prefix) ? $matches['version'][0] : $matches['version'][1];
            $parts = explode('.', $version);
            $this->version = $parts[0].'.0';
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
            'model'     => $this->GetModel(),
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

    public function GetModel()
    {
        return $this->model;
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

    private function parseOsDetails($os)
    {
        $this->os = $os;

        $startpoint = strpos($this->agent, '(');
        $length  = (strpos($this->agent, ')', $startpoint) - $startpoint) - 1;
        $verstring = substr($this->agent, $startpoint+1, $length);
        $details = explode(';', $verstring);
        if (is_array($details) !== true) {
            return;
        }

        switch($os)
        {
            case 'Android':
                $count       = count($details);
                $model       = explode(' Build', trim($details[$count == 5 ? 4 : 2]));
                $this->model = is_array($model) ? trim($model[0]) : '';
                $details     = explode(' ', trim($details[$count == 5 ? 2 : 1]));
                $this->osver = is_array($details) ? trim($details[1]) : '';
                break;
            case 'iOS':
                $model       = explode('/', trim($details[0]));
                $this->model = is_array($model) ? trim($model[0]) : '';
                $count       = count($details);
                $details     = explode(' ', trim($details[$count == 2 ? 1 : 2]));
                $this->osver = is_array($details) ? trim(str_replace('_', '.', $details[3])) : '';
                if (is_numeric($this->osver) !== true) {
                    $this->osver = '';
                }
                break;
            case 'macOS':
                $details     = explode(' ', trim($details[1]));
                $osver       = is_array($details) ? trim(str_replace('_', '.', $details[4])) : '';
                $this->osver = str_replace('_', '.', $osver);
                break;
            case 'Nintendo':
                $this->model = trim($details[0]);
                if (empty($this->browser)) {
                    $this->browser = 'Nintendo Browser';
                }
                if (empty($this->prefix)) {
                    $this->prefix  = $os;
                }
                break;
            case 'PlayStation':
                $model       = explode('/', trim($details[1]));
                $this->model = is_array($model) ? trim($model[0]) : '';
                $details     = explode('/', trim($model[1]));
                $this->osver = is_array($details) ? trim($details[0]) : '';
                break;
            case 'Windows':
                $details     = explode(' ', trim($details[0]));
                $this->osver = is_array($details) ? trim($details[2]) : '';
                break;
            case 'Windows Phone':
                $model       = explode('_', trim($details[3]));
                $this->model = is_array($model) ? trim($model[0]) : '';
                $details     = explode(' ', trim($details[0]));
                $this->osver = is_array($details) ? trim($details[2]) : '';
                break;
            case 'Xbox':
                $count       = count($details);
                $model       = explode('_', trim($details[$count == 5 ? 4 : 3]));
                $this->model = is_array($model) ? trim($model[0]) : '';
                break;
        }
    }
 }