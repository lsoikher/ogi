<?php
namespace Ghostmonitor\Api\Utils\Request;

use Ghostmonitor\Api\Utils\Request\Request_Interface;

class Request implements Request_Interface
{
    protected $wp;

    public function __construct(\WP $wp) {
        $this->wp = $wp;
    }

    public function get($key) {
        $ret = null;
        if( !empty($this->wp->query_vars[$key]) ) {
            $ret = $this->wp->query_vars[$key];
        }
        return $ret;
    }
}
