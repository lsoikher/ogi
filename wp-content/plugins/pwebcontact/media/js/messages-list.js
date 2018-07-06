/**
 * @package   Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license   GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 */

(function(window, document, $, undefined) {
  $(document).ready(function() {
    function getLocationSearchData() {
      var data = {};

      window.location.search.replace('?', '').split('&').map(function(param) {
        param = param.split('=');

        if (param && param instanceof Array) {
          var paramName = param[0] || '';
          var paramValue = param[1] || '';
          if (paramValue.length > 0) {
            data[paramName] = paramValue;
          }
        }
      });

      return data;
    }

    $('.pagination-links').on('click', 'a[data-page]', function(e) {
      e.preventDefault();
      e.stopPropagation();

      var data = getLocationSearchData();

      var targetPage = parseInt($(this).attr('data-page')) || -1;
      if (!isNaN(targetPage) && targetPage > 1) {
        data.p = targetPage
      } else {
        delete data.p;
      }

      var paramsList = Object.keys(data).map(function(key) {
        return key + '=' + data[key];
      });

      window.location.search = '?'+ paramsList.join('&');
    });

    $('.o-datepicker').on('change', function(e) {
      var self = $(this);
      var dateObj = self.datepicker('getDate') || null;

      var data = getLocationSearchData();

      if (dateObj) {
        var startDate = $('#filter_start_date').datepicker('getDate') || 0;
        var startDateTimestamp = startDate ? +startDate : null;

        var endDate = $('#filter_end_date').datepicker('getDate') || 0;
        var endDateTimestamp = endDate ? +endDate : null;

        if (startDateTimestamp > endDateTimestamp && endDateTimestamp > 0) {
          e.preventDefault();
          e.stopPropagation();

          return false;
        } else {
          if (startDateTimestamp > 0) {
            var day = startDate.getDate();
            day = day < 10 ? '0' + day : day;

            var month = startDate.getMonth() + 1;
            month = month < 10 ? '0' + month : month;

            data.start_date = startDate.getFullYear() + '-' + month + '-' + day;

            month = day = null;
          } else {
            delete data.start_date;
          }

          if (endDateTimestamp > 0) {
            var day = endDate.getDate();
            day = day < 10 ? '0' + day : day;

            var month = endDate.getMonth() + 1;
            month = month < 10 ? '0' + month : month;

            data.end_date = endDate.getFullYear() + '-' + month + '-' + day;

            month = day = null;
          } else {
            delete data.end_date;
          }
        }
      } else {
        delete data[self.attr('id').replace('filter_', '')];
      }

      var paramsList = Object.keys(data).map(function(key) {
        return key + '=' + data[key];
      });

      window.location.search = '?'+ paramsList.join('&');
    });

    $('.o-datepicker').datepicker();
  });
})(window, document, jQuery);
