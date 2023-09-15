<?php
    class SettingsController extends Controller {
        public function __construct()
        {
            parent::__construct();

            echo "<pre>SettingsController init'd</pre>";
            echo "<pre>ClassName: $this->className</pre>";
        }

        public function execute($params)
        {
            parent::execute($params);

            echo "<pre>SettingsController executed</pre>";
            echo "<pre>";
            print_r($_GET);
            echo "</pre>";
        }
    }