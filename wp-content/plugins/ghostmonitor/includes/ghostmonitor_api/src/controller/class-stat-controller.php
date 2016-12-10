<?php
namespace Ghostmonitor\Api\Controller;

use Ghostmonitor\Api\Controller\Base_Controller;

class Stat_Controller extends Base_Controller
{
    public function get_orders_action() {
        $start = $this->request->get('start');
        $end = $this->request->get('end');

        $orders = array();
        if( $start && $end ) {
            $orders = $this->get_model('order')->get_orders_between_dates($start, $end);

            $ret = array(
                'total' => 0,
                'total_count' => count($orders),
                'ghost_total' => 0,
                'ghost_count' => 0,
            );
            foreach ($orders as $order) {
                $order = new \WC_Order($order->ID);
                $ret['total'] += $order->get_subtotal();
            }
        }
        /*
            1. összes rendelés szám
            2. ghost db / összeg
            3.
        */
        $this->render_response( $ret );
    }

    // public function postOrdersAction() {
    //     echo $this->request->get('start');
    // }

    public function get_order_action( $id = 0 ) {
        echo $id;
    }

}
