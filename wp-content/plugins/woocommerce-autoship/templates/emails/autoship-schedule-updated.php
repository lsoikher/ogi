<p>
    Hello <?php echo $name ?>,<br><br>
    An update has been made to your WooCommerce Autoship schedule with id <?php echo $schedule_id ?>. The details of this schedule are listed below:
</p>

<ul>
    <li>Status: <?php echo $schedule_status ?></li>
    <li>Next order date: <?php echo $next_ship_date; ?></li>
    <li>Shipping method: <?php echo $shipping_method_id; ?></li>
    <li>Payment method: <?php echo $payment_method->get_display_name(); ?></li>
    <li>Items:</li>
</ul>
<table>
    <tr>
        <th>Product</th><th>Quantity</th>
    </tr>
    <?php
    foreach ( $items as $item => $qty ) {
        echo "<tr><td align='center'>$item</td><td align='center'>$qty</td></tr>";
    }
    ?>
</table>
