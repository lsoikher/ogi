<?php
namespace Ghostmonitor\Api\Model;

use WP_Query;
use \DateTime;

class Order_Model {

    /**
    * @var array
    */
    protected $postTypes;

    public function __construct() {
        $this->postTypes = 'shop_order';//wc_get_order_types();
    }

    /*÷
    *  @todo pimp query to handle hour and minute
    */
    public function get_orders_between_dates( $start, $end ) {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $args = array(
            'post_type'   => $this->postTypes,
            'post_status' => array_keys( wc_get_order_statuses() ),
            'date_query' => array(
                'compare' => 'BEETWEEN',
                'before' => array(
                    'year' => $endDate->format('Y'),
                    'month' => $endDate->format('m'),
                    'day' => $endDate->format('d'),
                ),
                'after' => array(
                    'year' => $startDate->format('Y'),
                    'month' => $startDate->format('m'),
                    'day' => $startDate->format('d'),
                ),
            )
        );

        $query = new WP_Query( $args );
        return $query->get_posts();
    }
}
