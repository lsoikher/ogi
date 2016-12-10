function SgContactForm() {

	var textInputWidth = contactFrontend.inputsWidth;
	var btnWidth = contactFrontend.buttnsWidth;
	var tetxInputsHeight = contactFrontend.inputsHeight;
	var btnHeight = contactFrontend.buttonHeight;
	var inProgresTitle = contactFrontend.procesingTitle;
}

SgContactForm.prototype.changeDimensionMode = function(dimension) {
	var size;
	size =  parseInt(dimension)+"px";
	if(dimension.indexOf("%") != -1 || dimension.indexOf("px") != -1) {
		size = dimension;
	}
	return size;
}

/*
 	Seters and getters
*/

SgContactForm.prototype.toggleVisible = function(toggleElement, elementStatus) {
	if(elementStatus) {
		toggleElement.css({'display': 'block'});
	}
	else {
		toggleElement.css({'display': 'none'});
	}
}

SgContactForm.prototype.binding = function() {
	var that = this;
	jQuery(".js-checkbox-acordion").bind('click', function() {
		var isCecked = jQuery(this).is(":checked");
		var elementClassName = jQuery(this).attr("data-contact-rel");
		var element = jQuery("."+elementClassName+"");
		that.toggleVisible(element, isCecked);
	});
	jQuery(".js-checkbox-acordion").each(function() {
		var isCecked = jQuery(this).is(":checked");
		var elementClassName = jQuery(this).attr("data-contact-rel");
		var element = jQuery("."+elementClassName+"");
		that.toggleVisible(element, isCecked);
	})
}

SgContactForm.prototype.changeBorderWidth = function() {
	var that = this;
	jQuery('[name="contact-inputs-border-width"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.setupBorderWidth(className, value);
	});
}

SgContactForm.prototype.changeInputsWIdth = function() {
	var that = this;
	jQuery('[name="contact-inputs-width"]').change(function() {
		var value = jQuery(this).val();
		var classname = jQuery(this).attr('data-contact-rel');
		that.addInputWidth(classname, value);
	});
}

SgContactForm.prototype.changeInputsHight = function() {
	var that = this;
	jQuery('[name="contact-inputs-height"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputsHeight(className, value);
	})
}

SgContactForm.prototype.changeBtnWidth = function() {
	var that = this;
	jQuery('[name="contact-btn-width"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputWidth(className, value);
	});
}

SgContactForm.prototype.changeBtnHeight = function() {
	var that = this;
	jQuery('[name="contact-btn-height"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputsHeight(className, value);
	});
}

SgContactForm.prototype.changeAreaWidth = function() {
	var that = this;
	jQuery('[name="contact-area-width"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputWidth(className, value);
	});
}

SgContactForm.prototype.changeAreaHeight = function() {
	var that = this;
	jQuery('[name="contact-area-height"]').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputsHeight(className, value);
	});
}

SgContactForm.prototype.changeTextAreaWidth = function() {
	var that = this;
	jQuery('contact-area-width').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputWidth(className, value);
	})
}

SgContactForm.prototype.changeTextAreaHeight = function() {
	var that = this;
	jQuery('contact-area-height').change(function() {
		var value = jQuery(this).val();
		var className = jQuery(this).attr('data-contact-rel');
		that.addInputsHeight(className, value);
	})
}

SgContactForm.prototype.textAreaResizeState = function() {
	var that = this;
	jQuery('[name="sg-contact-resize"]').change(function() {
		var value = jQuery(this).val();
		that.addTextAreastatState(value);
	});
}

SgContactForm.prototype.changeLabels = function() {
	jQuery(".sg-contact-fileds[data-contact-rel]").each(function() {
		jQuery(this).bind("input", function() {
			var className = jQuery(this).attr("data-contact-rel");
			var placeholderText = jQuery(this).val();
			jQuery("."+className).attr("placeholder", placeholderText);
		});
	});
}

SgContactForm.prototype.changeButtonTitle = function() {
	var that = this;

	jQuery("[name='contact-btn-title']").bind('input', function() {
		var className = jQuery(this).attr("data-contact-rel");
		var val = jQuery(this).val();
		that.setupButtonText("."+className,val);
	});
	jQuery("[name='contact-btn-title']").trigger('input');
}

SgContactForm.prototype.fieldsColor = function(element) {
	var color = element.val();
	var classname = element.attr('data-contact-rel');
	this.setupBackgroundColor("."+classname, color);
	var textAreaClass = element.attr('data-contact-area-rel');
	this.setupBackgroundColor("."+textAreaClass, color);
}

SgContactForm.prototype.fieldsBorderColor = function(element) {
	var color = element.val();
	var classname = element.attr('data-contact-rel');
	this.setupBorderColor('.'+classname, color);
	var textAreaClass = element.attr('data-contact-area-rel');
	this.setupBorderColor('.'+textAreaClass, color);
}

SgContactForm.prototype.buttonTextColor = function(element) {
	var color = element.val();
	var classname = element.attr('data-contact-rel');
	this.setupButtonColor("."+classname, color);
	var textAreaClass = element.attr('data-contact-area-rel');
	this.setupButtonColor("."+textAreaClass, color);
}

SgContactForm.prototype.placeholderColor = function(element) {
	var color = element.val();
	var classname = element.attr('data-contact-rel');
	var textAreaClass = element.attr('data-contact-area-rel');
	classes = [classname,textAreaClass];

	this.setupPlaceholderColor(classes, color);
}

SgContactForm.prototype.colorpickerChange = function() {
	var that = this;
	jQuery('.sg-contact-btn-color').wpColorPicker({ /*Inputs  and text area color */
		change: function() {
			var sgColorpicker = jQuery(this);
			that.fieldsColor(sgColorpicker);
		}
	});

	jQuery('.sg-contact-btn-border-color').wpColorPicker({ /*Inputs and text area border color */
		change: function() {
			var sgColorpicker = jQuery(this);
			that.fieldsBorderColor(sgColorpicker);
		}
	});

	jQuery('.sg-contact-btn-text-color').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			that.buttonTextColor(sgColorpicker);
		}
	});

	jQuery('.sg-contact-placeholder-color').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			that.placeholderColor(sgColorpicker);
		}
	});
	jQuery(".wp-picker-holder").each(function() {

		jQuery(this).bind('click', function() {
			buttonColor = jQuery(this).prev().find('.sg-contact-btn-text-color');
			inputsBorderColor = jQuery(this).prev().find('.sg-contact-btn-border-color');
			inputsColor = jQuery(this).prev().find('.sg-contact-btn-color');
			placeholderColor = jQuery(this).prev().find('.sg-contact-placeholder-color');

			if(buttonColor.length) {
				that.buttonTextColor(buttonColor);
			}
			if(inputsBorderColor.length) {
				that.fieldsBorderColor(inputsBorderColor);
			}
			if(inputsColor.length) {
				that.fieldsColor(inputsColor);
			}
			if(placeholderColor.length) {
				that.placeholderColor(placeholderColor)
			}
		});
	});
}

SgContactForm.prototype.setupBackgroundColor = function(element, color) {
	jQuery(element).each(function() {
		jQuery(this).css({'background': color});
	});
}

SgContactForm.prototype.setupBorderColor = function(element, color) {
	jQuery(element).each(function() {
		jQuery(this).css({'border-color': color});
	});
}

SgContactForm.prototype.setupButtonColor = function(element, color) {
	jQuery(element).css({'color': color});
}

SgContactForm.prototype.setupButtonText = function(element, value) {
	jQuery(element).val(value);
}

SgContactForm.prototype.setupPlaceholderColor = function(elements, color) {
	var styleContent = '';
	for(element in elements) {
		element = elements[element];

		jQuery("."+element).each(function() {
			jQuery("#sg-placeholder-style").remove()
			styleContent += '.'+element+'::-webkit-input-placeholder {color: ' + color + ';} .'+element+'::-moz-placeholder {color: ' + color + ';} .'+element+':-ms-input-placeholder {color:$sgSubsPlaceholderColor;}';

		});
		var styleBlock = '<style id="sg-placeholder-style">' + styleContent + '</style>';
		jQuery('head').append(styleBlock);
	}
}

SgContactForm.prototype.addInputWidth = function(classname, width) {
	var width = this.changeDimensionMode(width);
	jQuery("."+classname).each(function() {
		jQuery(this).css({"width": width,'maxWidth': '100%'});
	});
}

SgContactForm.prototype.setupBorderWidth = function(className, value) {
	var value = parseInt(value)+"px";
	jQuery("."+className).css({'border-width': value});
}

SgContactForm.prototype.addInputsHeight = function(className, value) {
	var height = this.changeDimensionMode(value);
	jQuery("."+className).each(function() {
		jQuery(this).css({"height": height});
	});
}

SgContactForm.prototype.addTextAreastatState = function(value) {
	jQuery('.js-contact-text-area').css({'resize': value});
}

SgContactForm.prototype.shake = function() {
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

SgContactForm.prototype.addFieldsColor = function() {
	placeholderClasses = ['js-contact-text-area', 'js-contact-text-inputs']
	this.setupPlaceholderColor(placeholderClasses, contactFrontend.placeholderColor);
	this.setupButtonColor('.js-contact-submit-btn', contactFrontend.btnTextColor);
	this.setupButtonColor('.js-contact-text-inputs', contactFrontend.inputsColor);
	this.setupButtonColor('.js-contact-text-area', contactFrontend.inputsColor);
	this.setupBackgroundColor('.js-contact-submit-btn', contactFrontend.btnBackgroundColor);
	this.setupBackgroundColor('.js-contact-text-inputs', contactFrontend.inputsBackgroundColor);
	this.setupBackgroundColor('.js-contact-text-area', contactFrontend.inputsBackgroundColor);
	this.setupBorderColor('.js-contact-text-inputs', contactFrontend.inputsBorderColor);

}

SgContactForm.prototype.addFieldsDiemntions = function() {
	this.addInputWidth('js-contact-text-inputs', contactFrontend.inputsWidth);
	this.setupBorderWidth('js-contact-text-inputs', contactFrontend.contactInputsBorderWidth);
	this.addInputsHeight('js-contact-text-inputs', contactFrontend.inputsHeight);
	this.addInputWidth('js-contact-submit-btn', contactFrontend.buttnsWidth);
	this.addInputsHeight('js-contact-submit-btn', contactFrontend.buttonHeight);
	this.addInputWidth('js-contact-text-area', contactFrontend.contactAreaWidth);
	this.addInputsHeight('js-contact-text-area', contactFrontend.contactAreaHeight);
	this.addTextAreastatState(contactFrontend.contactResize)
}

SgContactForm.prototype.sgWpAjax = function() {
	var that = this;
	var textWidth = contactFrontend.inputsWidth;

	var validationMessage = contactFrontend.validateMessage;
	var isNameRequired = contactFrontend.sgContactNameRequired;
	var isSubjectRequired = contactFrontend.sgContactSubjectRequired;

	jQuery('#sg-contact-data').on('submit', function(event) {
		event.preventDefault();
		var requeiredFields = [];
		var sgContactData = jQuery(this).serialize();
		var receiveMail =  contactFrontend.receiveEmail;
		var email = jQuery(".js-contact-email").val();
		var validate = email.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/);

		jQuery('.js-requierd-style').remove();
		jQuery('.sg-contact-required').each(function() {
			if(jQuery(this).val() == '') {
				if(isNameRequired == '' && jQuery(this).attr('name')  == 'contact-name' ) {
					return;
				}
				if(isSubjectRequired == '' && jQuery(this).attr('name')  == 'contact-subject') {
					return;
				}
				requeiredFields.push(jQuery(this));
				jQuery(this).after("<span class='js-requierd-style'>"+validationMessage+"</span>");
			}
		});
		if(requeiredFields.length != 0) {
			return;
		}
		if(!validate) {
			jQuery('.js-contact-email').shake();
			jQuery('.js-validate-email').removeClass('sg-js-hide');
			jQuery('.js-validate-email').css({
				'width': textWidth,
				'margin': '0px auto 5px auto',
				'font-size': '12px',
				'color': 'red',
				'display': 'block'
			});
			return;
		}
		var data = {
			action: 'contact_send_mail',
			beforeSend: function() {
				jQuery('.js-contact-submit-btn').val(contactFrontend.procesingTitle).attr('disabled', 'disabled');
			},
			contactParams: sgContactData,
			receiveMail: receiveMail
		}

		jQuery.post(contactFrontend.ajaxurl, data, function(response, d) {
	
			if(response == true) {
				jQuery('#sg-contact-data').css({'display': 'none'});
				jQuery('#sg-contact-success').removeClass('sg-js-hide');
				jQuery.sgcolorbox.resize();
			}
			else {
				jQuery("#sg-contact-faild").removeClass('sg-js-hide');
			}
			

		});
	});
}

SgContactForm.prototype.buildStyle = function() {
	this.shake();
	this.sgWpAjax();
	this.addFieldsColor();
	this.addFieldsDiemntions();
}

SgContactForm.prototype.livePreview = function() {
	this.changeLabels();
	this.changeButtonTitle();
	this.colorpickerChange();
	this.changeBorderWidth();
	this.changeInputsWIdth();
	this.changeInputsHight();
	this.changeBtnWidth();
	this.changeBtnHeight();
	this.changeAreaWidth();
	this.changeAreaHeight();
	this.changeTextAreaWidth();
	this.changeTextAreaHeight();
	this.textAreaResizeState();
	this.binding();
}
