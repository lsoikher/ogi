function SgSubscription() {
	var firstNameStatus;
	var lastNameStatus;
	var textInputWidth;
	var btnWidth;
	var tetxInputsHeight;
	var btnHeight;
	var inProgresTitle;
}

SgSubscription.prototype.changeDimensionMode = function(dimension) {
	var size;
	size =  parseInt(dimension)+"px";
	if(dimension.indexOf("%") != -1 || dimension.indexOf("px") != -1) {
		size = dimension;
	}
	return size;
}

SgSubscription.prototype.setFirstNameStatus = function(firstNameStatus) {
	this.firstNameStatus = firstNameStatus;
}

SgSubscription.prototype.getFirstNameStatus = function() {
	return this.firstNameStatus;
}

SgSubscription.prototype.setLastNameStatus = function(lastNameStatus) {
	this.lastNameStatus = lastNameStatus;
}

SgSubscription.prototype.setInProgresTitle = function(title) {
	this.inProgresTitle = title;
}

SgSubscription.prototype.getInProgresTitle = function() {
	return this.inProgresTitle;
}

SgSubscription.prototype.getLastNameStatus = function() {
	return this.lastNameStatus;
}

SgSubscription.prototype.setTextInputWidth = function(width) {
	this.textInputWidth = this.changeDimensionMode(width);
}

SgSubscription.prototype.setTextInputsHeight = function(height) {
	this.tetxInputsHeight = this.changeDimensionMode(height);
}

SgSubscription.prototype.getTextInputsHeight = function() {
	return this.tetxInputsHeight;
}

SgSubscription.prototype.setBtnHeight = function(height) {
	this.btnHeight = this.changeDimensionMode(height);
}

SgSubscription.prototype.getBtnHeight = function() {
	return this.btnHeight;
}

SgSubscription.prototype.getTextInputWidth = function() {
	return this.textInputWidth;
}

SgSubscription.prototype.setBtnWidth = function(width) {
	this.btnWidth = width;
}

SgSubscription.prototype.getBtnWidth = function(width) {
	return this.btnWidth;
}

/*
 	Seters and getters
*/

SgSubscription.prototype.toggleVisible = function(toggleElement, elementStatus) {
	if(elementStatus) {
		toggleElement.css({'display': 'block'});
	}
	else {
		toggleElement.css({'display': 'none'});
	}
}

SgSubscription.prototype.binding = function() {
	var that = this;
	jQuery(".js-checkbox-acordion").bind('click', function() {
		var isCecked = jQuery(this).is(":checked");
		var elementClassName = jQuery(this).attr("data-subs-rel");
		var element = jQuery("."+elementClassName+"");
		that.toggleVisible(element, isCecked);
	});
	jQuery(".js-checkbox-acordion").each(function() {
		var isCecked = jQuery(this).is(":checked");
		var elementClassName = jQuery(this).attr("data-subs-rel");
		var element = jQuery("."+elementClassName+"");
		that.toggleVisible(element, isCecked);
	})
}

SgSubscription.prototype.changeBorderWidth = function() {
	var that = this;
	jQuery('[name="subs-text-border-width"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-subs-rel');
		that.setupBorderWidth(className, value);
	});
}

SgSubscription.prototype.changeLabels = function() {
	jQuery(".sg-subs-fileds[data-subs-rel]").each(function() {
		jQuery(this).bind("input", function() {
			var className = jQuery(this).attr("data-subs-rel");
			var placeholderText = jQuery(this).val();
			jQuery("."+className).attr("placeholder", placeholderText);
		});
	});
}

SgSubscription.prototype.changeButtonTitle = function() {
	var that = this;
	jQuery("[name='subs-btn-title']").change(function() {
		var className = jQuery(this).attr("data-subs-rel");
		var val = jQuery(this).val();
		that.setupButtonText("."+className,val);
	});
}

SgSubscription.prototype.colorpickerChange = function() {
	var that = this;
	jQuery('.sg-subs-btn-color').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			var color = sgColorpicker.val();
			var classname = sgColorpicker.attr('data-subs-rel');
			that.setupBackgroundColor("."+classname, color);
		}
	});
	jQuery('.sg-subs-btn-border-color').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			var color = sgColorpicker.val();
			var classname = sgColorpicker.attr('data-subs-rel');
			that.setupBorderColor('.'+classname, color);
		}
	});
	jQuery('.sg-subs-btn-text-color').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			var color = sgColorpicker.val();
			var classname = sgColorpicker.attr('data-subs-rel');
			that.setupButtonColor("."+classname, color);
		}
	});
	jQuery('.sg-subs-placeholder-color').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			var color = sgColorpicker.val();
			var classname = sgColorpicker.attr('data-subs-rel');
			that.setupPlaceholderColor(classname,color);
		}
	});
	jQuery(".wp-picker-holder").bind('click',function() {
		var selectedInput = jQuery(this).prev().find('.sgOverlayColor');
	});
}

SgSubscription.prototype.setupBackgroundColor = function(element, color) {
	jQuery(element).each(function() {
		jQuery(this).css({'background': color});
	});
}

SgSubscription.prototype.setupBorderColor = function(element, color) {
	jQuery(element).each(function() {
		jQuery(this).css({'border-color': color});
	});
}

SgSubscription.prototype.setupButtonColor = function(element, color) {
	jQuery(element).css({'color': color});
}

SgSubscription.prototype.setupButtonText = function(element, value) {
	jQuery(element).val(value);
}

SgSubscription.prototype.setupPlaceholderColor = function(element, color) {
	jQuery("."+element).each(function() {
		jQuery("#sg-placeholder-style").remove()
		var styleContent = '.'+element+'::-webkit-input-placeholder {color: ' + color + ';} .'+element+'::-moz-placeholder {color: ' + color + ';} .'+element+':-ms-input-placeholder {color:$sgSubsPlaceholderColor;}';
		var styleBlock = '<style id="sg-placeholder-style">' + styleContent + '</style>';
		jQuery('head').append(styleBlock);
	});
}

SgSubscription.prototype.livePreview = function() {
	this.binding();
	this.changeLabels();
	this.changeButtonTitle();
	this.colorpickerChange();
	this.changeButtonTitle();
	this.changeBorderWidth();
}

SgSubscription.prototype.addInputWidth = function() {
	var inputsWidth = this.getTextInputWidth();
	jQuery(".js-subs-text-inputs").each(function() {
		jQuery(this).css({"width": inputsWidth,'maxWidth': '100%'});
	});
}

SgSubscription.prototype.setupBorderWidth = function(className, value) {
	var value = parseInt(value)+"px";
	jQuery("."+className).css({'border-width': value});
}

SgSubscription.prototype.addInputsHeight = function() {
	var height = this.getTextInputsHeight();
	jQuery(".js-subs-text-inputs").each(function() {
		jQuery(this).css({"height": height});
	});
}

SgSubscription.prototype.addBtnWidth = function() {
	var width = this.getBtnWidth();
	jQuery(".js-subs-submit-btn").css({"width": width,'maxWidth': '100%'});
}

SgSubscription.prototype.addBtnHeight = function() {
	var height = this.getBtnHeight();
	jQuery(".js-subs-submit-btn").css({'height': height});
}

SgSubscription.prototype.shake = function() {
	jQuery.fn.shake = function(interval,distance,times) {
		interval = typeof interval == "undefined" ? 100 : interval;
		distance = typeof distance == "undefined" ? 10 : distance;
		times = typeof times == "undefined" ? 3 : times;
		var jTarget = jQuery(this);
		jTarget.css('position','relative');
		for(var iter=0;iter<(times+1);iter++){
			jTarget.animate({ left: ((iter%2==0 ? distance : distance*-1))}, interval);
		}
		return jTarget.animate({ left: 0},interval);
	}
}

SgSubscription.prototype.sgWpAjax = function() {
	var that = this;
	var textWidth = this.getTextInputWidth();
	jQuery('#sg-subscribers-data').on('submit', function(event) {
		event.preventDefault();
		var sgSubscribersData = jQuery(this).serialize();
		var email = jQuery(".js-subs-email-name").val();
		var validate = email.search(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/);

		if(validate == -1) {
			jQuery('.js-subs-email-name').shake();
			jQuery('.js-validate-email').removeClass('sg-js-hide');
			jQuery('.js-validate-email').css({
				'width': textWidth,
				'margin': '0px auto 5px auto',
				'font-size': '12px',
				'color': 'red'
			});
			return;
		}
		jQuery('.js-validate-email').addClass('sg-js-hide');
		var data = {
			action: 'subs_send_mail',
			beforeSend: function() {
				jQuery('.js-subs-submit-btn').val(that.getInProgresTitle()).attr('disabled', 'disabled');
		    },
			subsribers: sgSubscribersData
		}
		jQuery.post(SgSubscriptionParams.ajaxurl, data, function(response,d) {
			if(SgSubscriptionParams.subsSontShowAfterSubmitting) {
				jQuery.sgcolorbox.close();
				that.dontShowSubscribedUsers();
			}
			else {
				jQuery('#sg-subscribers-data').css({'display': 'none'});
				jQuery('.sg-subs-success').removeClass('sg-js-hide');
			}
		});
	});
}

SgSubscription.prototype.dontShowSubscribedUsers = function() {
	var id = SgSubscriptionParams.popupId;

	jQuery.cookie("subscription"+id,'1', { expires: 365});
}

SgSubscription.prototype.init = function() {

	this.setTextInputWidth(SgSubscriptionParams.textInputsWidth);
	this.setBtnWidth(SgSubscriptionParams.sgSubsBtnWidth);
	this.setBtnHeight(SgSubscriptionParams.sgSubsBtnHeight);
	this.setTextInputsHeight(SgSubscriptionParams.sgSubsTextHeight);
	this.setupBackgroundColor('.js-subs-text-inputs', SgSubscriptionParams.textInputsBgColor);
	this.setupBackgroundColor('.js-subs-submit-btn', SgSubscriptionParams.submitButtonBgColor);
	this.setupBorderColor('.js-subs-text-inputs', SgSubscriptionParams.sgSubsTextBordercolor);
	this.setupButtonColor('.js-subs-text-inputs', SgSubscriptionParams.sgSubsInputsColor);
	this.setupButtonColor('.js-subs-submit-btn', SgSubscriptionParams.subsButtonColor);
	this.setupButtonText('.js-subs-submit-btn', SgSubscriptionParams.sgSubsBtnTitle);
	this.setInProgresTitle(SgSubscriptionParams.sgSubsBtnProgressTitle);
	this.setupBorderWidth('js-subs-text-inputs', SgSubscriptionParams.sgSubstextBorderWidth);

	this.shake();
	this.addInputWidth();
	this.addBtnWidth();
	this.addBtnHeight();
	this.addInputsHeight();
	this.sgWpAjax();
}
