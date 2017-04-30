jQuery(function ($) {
	function sortFrequencyOptions() {
		var options = $('.wc-autoship-frequency-option').toArray();
		var changed = false;
		do {
			changed = false;
			for (var l = 0, r = 1; r < options.length; l++, r++) {
				var $left = $(options[l]);
				var $right = $(options[r]);
				if ($right.data('frequency') < $left.data('frequency')) {
					var temp = options[l];
					options[l] = options[r];
					options[r] = temp;
					changed = true;
				}
			}
		} while (changed);
		for (var i = 0; i < options.length; i++) {
			$('#wc-autoship-product-page-frequency-options-body').append(options[i]);
		}
	}
	
	$('#wc-autoship-product-page-frequency-button').click(function () {
		var frequency = parseInt($('#wc-autoship-product-page-frequency').val());
		var name = $('#wc-autoship-product-page-frequency-name').val();
		if (isNaN(frequency) || frequency < 7 || frequency > 365 || name == '') {
			return;
		}
		var options = $('.wc-autoship-frequency-option').toArray();
		for (var i = 0; i < $('.wc-autoship-frequency-option').length; i++) {
			if ($(options[i]).data('frequency') == frequency) {
				alert('This frequency option already exists!');
				return;
			}
		}
		// Frequency option row
		var $frequencyOption = $('<tr class="wc-autoship-frequency-option"></tr>')
			.attr('id', 'wc-autoship-frequency-option-' + frequency)
			.data('frequency', frequency);
		// Frequency column
		var $frequencyColumn = $('<td class="wc-autoship-frequency-option-frequency-column"></td>');
		var $input = $('<input type="hidden" />')
			.attr('name', 'wc_autoship_product_page_frequency_options_array[' + frequency + ']')
			.val(name);
		$frequencyColumn.append($input);
		$frequencyColumn.append($('<span></span>').text(frequency));
		$frequencyOption.append($frequencyColumn);
		// Name column
		var $nameColumn = $('<td class="wc-autoship-frequency-option-name-column"></td>');
		$nameColumn.text(name);
		$frequencyOption.append($nameColumn);
		// Delete column
		var $deleteColumn = $('<td class="wc-autoship-frequency-option-delete-column"></td>');
		var $deleteButton = $('<button type="button" class="wc-autoship-frequency-option-delete">&times;</button>');
		$deleteButton.click(function () {
			$(this).parents('.wc-autoship-frequency-option').remove();
		});
		$deleteColumn.append($deleteButton);
		$frequencyOption.append($deleteColumn);
		// Append row
		$('#wc-autoship-product-page-frequency-options-body').append($frequencyOption);
		$('#wc-autoship-product-page-frequency').val('').focus();
		$('#wc-autoship-product-page-frequency-name').val('');
		sortFrequencyOptions();
	});
	
	var days = [];
	for ( var d = 7; d < 366; d++ ) {
		days.push(d.toString());
	}
	$('#wc-autoship-product-page-frequency').autocomplete({
		source: days,
		minLength: 0
	}).focus(function () {
		$(this).autocomplete('search');
	});
	
	$('#wc-autoship-product-page-frequency-name').keypress(function (e) {
		 var key = e.which;
		 if (key == 13) {
			 $('#wc-autoship-product-page-frequency-button').click();
			 return false;
		 }
	});

	$('.wc-autoship-frequency-option-delete').click(function () {
		$(this).parents('.wc-autoship-frequency-option').remove();
	});
});