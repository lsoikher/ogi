<?php
/*
    Plugin Name: Ghostmonitor API Endpoint
    Description: Adds an API endpoint at /api/ghostmonitor/
    Version: 0.1
    Author: Ghostmonitor INC
    Author URL: http://www.ghostmonitor.com

    Example URL:
    /api/ghostmonitor/stat/orders?token=12312&start=2015-10-21&end=2015-10-25
*/

class Ghost_API
{
    private static $api_page_name = "api/ghostmonitor/";

    public function __construct() {
        add_action('init', array($this, 'add_endpoint'));
        add_action('parse_request', array($this, 'sniff_requests'), 0);
        add_filter( 'query_vars', array($this, 'add_query_vars') );

        spl_autoload_register(__NAMESPACE__ . '\Ghost_API::autoloader');
    }

    public static function autoloader($class) {
        $prefix = 'Ghostmonitor\Api\\';
        $base_dir = __DIR__ . '/src/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $search = array('_', '\\');
        $replace = array('-', '/');

        $relative_class = str_replace( $search, $replace, strtolower( substr( $class, $len ) ) );

        $path_array = explode('/', $relative_class);
        $class = 'class-'.end( $path_array );
        $path_array[count($path_array)-1] = $class;
        $file = $base_dir . implode('/',$path_array) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    public function add_query_vars( $query_vars ) {
        $query_vars = array_merge(array_keys($_GET), $query_vars);
        return $query_vars;
    }

    /**
    *   Add API Endpoint
    *   @return void
    */
    public function add_endpoint() {
        add_rewrite_endpoint( self::$api_page_name, EP_ROOT );
    }

    /**
    * sniffRequest...
    */
    public function sniff_requests() {
        global $wp;
        if(isset($wp->query_vars['pagename']) && strpos($wp->query_vars['pagename'], self::$api_page_name) !== false ) {
            $this->parse_request($wp);
            exit;
        }
    }

    /**
    *
    */
    private function parse_request($wp) {
        $params_string = str_replace(self::$api_page_name, '', $wp->query_vars['pagename']);
        list($controller, $method) = explode('/', $params_string);

        $class = 'Ghostmonitor\Api\Controller\\'.ucfirst($controller).'_Controller';
        $method = $this->get_http_method().'_'.ucfirst($method).'_action';

        if( class_exists($class) && method_exists($class, $method) && $this->is_valid_token($wp->query_vars['token']) ) {
            global $woocommerce;
            $request = new Ghostmonitor\Api\Utils\Request\Request($wp);
            $parameters = $this->get_function_parameters($params_string, $wp->query_vars);
            return call_user_func_array(array(new $class($request, $woocommerce), $method), $parameters);
        } else {
            throw new \Exception("The endpoint you've tried to access does not exist!");
        }
    }

    private function get_function_parameters($params_string, $query_variables) {
        $parameters = array_slice(explode('/', $params_string), 2);
        if( !empty($query_variables['page']) )  {
            $parameters[] = str_replace('/', '', $query_variables['page']);
        }
        return $parameters;
    }

    private function get_http_method() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
    * @todo validate $token
    */
    private function is_valid_token($token) {
        return true;
    }
}



