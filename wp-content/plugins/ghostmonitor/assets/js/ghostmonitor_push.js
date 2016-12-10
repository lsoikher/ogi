jQuery(function ($) {
  $(document).ready(function () {
    $('body').on('added_to_cart updated_wc_div', function () {
      var req = { action: 'gm_send_cart_data' };

      $.get(GhostMonitorAjax.ajax_url, req).done(function (data) {
        if((!data.hasOwnProperty('setCartData') && !data.hasOwnProperty('setCartItem'))) {
          return;
        }

        delete data.setCartData.session_id;
        _ghostmonitor.push(['setCartData', data.setCartData]);

        data.setCartItem.forEach(function (elem) {
          delete elem.session_id;
          _ghostmonitor.push(['setCartItem', elem]);
        })
      })
    })
  })
});
