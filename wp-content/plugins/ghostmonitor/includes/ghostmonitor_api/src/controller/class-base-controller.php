<?php
namespace Ghostmonitor\Api\Controller;

use Ghostmonitor\Api\Utils\Response\Response;
use Ghostmonitor\Api\Utils\Request\Request_Interface;
use \WooCommerce;
use \Exception;

abstract class Base_Controller
{
    /*
    * @var Request_Interface
    */
    protected $request;

    /*
    * @var WooCommerce
    */
    protected $woocommerce;

    public function __construct( Request_Interface $request, WooCommerce $woocommerce ) {
        $this->request = $request;
        $this->woocommerce = $woocommerce;
    }

    /**
    *
    * @var string $model_name
    * @throws \Exception
    * @return object
    */
    public function get_model( $model_name ) {
        $model_class = 'Ghostmonitor\Api\Model\\'.ucfirst( $model_name ).'_Model';
        if( class_exists( $model_class ) ) {
            return new $model_class();
        } else {
            throw new \Exception( $model_name.' not found!' );
        }
    }

    /*
    *  @todo pimp Response
    */
    protected function render_response( array $data ) {
        $response = new Response();
        $response->render_json_response( $data );
    }
}
