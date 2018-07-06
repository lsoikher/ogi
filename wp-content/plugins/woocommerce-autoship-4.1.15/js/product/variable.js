jQuery(function ($) {
    $('.variations_form').on('found_variation', function (event, variation) {
        $('.wc-autoship-price').find('.amount').text('');
        $('.wc-autoship-price').hide();
        var variations = AUTOSHIP_PRODUCT_VARIABLE.variations;
        for (var v = 0; v < variations.length; v++) {
            if (variations[v].variation_id == variation.variation_id) {
                var autoship_price = variations[v].autoship_price;
                if (autoship_price) {
                    $('.wc-autoship-price').find('.amount').text(AUTOSHIP_PRODUCT_VARIABLE.currency_symbol + parseFloat(autoship_price).toFixed(2));
                    $('.wc-autoship-price').show();
                }
                break;
            }
        }
    });
});