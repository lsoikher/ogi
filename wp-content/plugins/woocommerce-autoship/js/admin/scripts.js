jQuery(function ($) {
    var $schedulesBulkAction = $('#schedules-filter').find('#bulk-action-selector-top');
    $schedulesBulkAction.change(function () {
        $('.wc-autoship-admin-bulk-extra-input').remove();
        if (this.value == 'update_next_order_date') {
            var $nextOrderDate = $('<input type="date" name="next_order_date" id="wc-autoship-admin-next-order-date" class="wc-autoship-admin-bulk-extra-input wc-autoship-datepicker" />');
            $nextOrderDate.insertAfter(this);
            var today = new Date();
            var year = today.getFullYear();
            var month = 1 + today.getMonth();
            if (month < 10) {
                month = '0' + month;
            }
            var day = today.getDate();
            if (day < 10) {
                day = '0' + day;
            }
            var dateString = year + '-' + month + '-' + day;
            $nextOrderDate.val(dateString);
            initWcAutoshipDatepicker();
        } else if (this.value == 'update_shipping_method_id') {
            var $shippingMethod = $('<input type="text" name="shipping_method_id" id="wc-autoship-admin-shipping-method-id" class="wc-autoship-admin-bulk-extra-input" placeholder="free_shipping:1" />');
            $shippingMethod.insertAfter(this);
        } else if (this.value == 'update_coupon') {
            var $coupon = $('<input type="text" name="coupon" id="wc-autoship-admin-coupon" class="wc-autoship-admin-bulk-extra-input" />');
            $coupon.insertAfter(this);
        } else if (this.value == 'change_autoship_frequency') {
            var $autoshipFrequency = $('<input type="number" name="autoship_frequency" id="wc-autoship-admin-autoship-frequency" class="wc-autoship-admin-bulk-extra-input" min="7" max="365" value="7" />');
            $autoshipFrequency.insertAfter(this);
        }
    });
});