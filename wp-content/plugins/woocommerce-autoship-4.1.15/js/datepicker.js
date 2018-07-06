function browserSupportsHtml5Date() {
    var el = document.createElement("input");
    try {
        el.type = "date";
    } catch (e) {
        return false;
    }
    return el.type === "date";
}

function initWcAutoshipDatepicker() {
    (function ($) {
        if (!browserSupportsHtml5Date()) {
            $('input[type=date].wc-autoship-datepicker').each(function (i, input) {
                input.setAttribute('type', 'text');
                var picker = new Pikaday({ field: input, format: 'YYYY-MM-DD' });
            });
        }
    })(jQuery);
}

jQuery(function ($) {
    initWcAutoshipDatepicker();
});
