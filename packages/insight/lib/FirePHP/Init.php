<?php

// This function is called at the bottom of this file
function FirePHP__main() {

    $activate = true;

    if(defined('FIREPHP_ACTIVATED') && constant('FIREPHP_ACTIVATED')===false) {
        $activate = false;
    }

    if($activate) {

        // Only activate FirePHP if certain header prefixes are found:
        //  * x-wf-
        //  * x-insight
        
        $headers = false;
        if(function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = $_SERVER;
        }
        $activate = false;
        foreach( $headers as $name => $value ) {
            $name = strtolower($name);
            if(substr($name, 0, 5) == 'http_') {
                $name = str_replace(' ', '-', str_replace('_', ' ', substr($name, 5)));
            }
            if(substr($name, 0, 5)=='x-wf-') {
                $activate = true;
            } else
            if(substr($name, 0, 9)=='x-insight') {
                $activate = true;
            }
        }
    }

    if($activate) {

        if(!defined('FIREPHP_ACTIVATED')) {
            define('FIREPHP_ACTIVATED', true);
        }

        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)));
        
        require_once('FirePHP/Insight.php');
        
        FirePHP::setInstance(new FirePHP_Insight());
        
        Insight_Helper__main();
    
    } else {
    
        class FirePHP {
            protected static $instance = null;
            public static function getInstance() {
                if(!self::$instance) {
                    self::$instance = new FirePHP();
                }
                return self::$instance;
            }
            public function getEnabled() {
                return false;
            }
            public function detectClientExtension() {
                return false;
            }
            public function __call($name, $arguments) {
                return self::getInstance();
            }
            public static function __callStatic($name, $arguments) {
                return self::getInstance();
            }
        }
    }
}

FirePHP__main();
