function SGAgeRestriction() {
	this.popupId = '';
}

SGAgeRestriction.prototype.setPopupId = function(id) {
	this.popupId = id;
}

SGAgeRestriction.prototype.getPopupId = function() {
	return this.popupId;
}

SGAgeRestriction.prototype.init = function() {
	var that = this;
	var id = SgAgeRestrcitionParams.id;
	this.setPopupId(id);

	restrictionData = SG_POPUP_DATA[id];
	yesButtonBackgroundColor = this.sgSafeStr(restrictionData['yesButtonBackgroundColor']);
	noButtonBackgroundColor = this.sgSafeStr(restrictionData['noButtonBackgroundColor']);
	yesButtonTextColor = this.sgSafeStr(restrictionData['yesButtonTextColor']);
	noButtonTextColor = this.sgSafeStr(restrictionData['noButtonTextColor']);
	yesButtonRadius = this.sgSafeStr(restrictionData['yesButtonRadius']);
	noButtonRadius = this.sgSafeStr(restrictionData['noButtonRadius']);

	jQuery('body').addClass('sg-hide-overflow'); 

	if(SgAgeRestrcitionParams.pushToBottom == 'on') { 
		jQuery('.buttons-wrapper').addClass('sg-push-to-bottom');
	}
	
	jQuery('#sgYesButton').on('click',function() {
		that.dontShowCurrentAdultUser();
		jQuery('body').removeClass('sg-hide-overflow');
		jQuery.sgcolorbox.close();
	});
	jQuery('#sgNoButton').bind('click',function() {
		jQuery('body').removeClass('sg-hide-overflow');
		if(SgAgeRestrcitionParams.restrictionUrl == '' ) {
			jQuery.sgcolorbox.close();
		}
		else {
			window.location = SgAgeRestrcitionParams.restrictionUrl;
		}
	});

	jQuery('#sgYesButton').css({
		'background-color' : yesButtonBackgroundColor,
		'color' : yesButtonTextColor,
		'border-radius': yesButtonRadius+"%",
		'height' : '20px !important',
		'border-color' : yesButtonBackgroundColor,
		'padding': '12px',
		'border' : 'none',
		'font-weight' : 'bold',
		'font-size' : '15px'
	});

	jQuery('#sgNoButton').css({
		'background-color' : noButtonBackgroundColor,
		'color' : noButtonTextColor,
		'borderRadius' : noButtonRadius+"%",
		'height' : '20px !important',
		'border-color' : noButtonBackgroundColor,
		'padding': '12px',
		'border' : 'none',
		'font-weight' : 'bold',
		'font-size' : '15px'
	});

};

SGAgeRestriction.prototype.dontShowCurrentAdultUser = function() {
	var id = this.getPopupId();
		
	sgCookieData = {
		'popupId': id,
		'openCounter': 2,
		'openLimit': 1
	}
	jQuery.cookie("sgPopupDetails"+id,JSON.stringify(sgCookieData), { expires: 365});
}

SGAgeRestriction.prototype.sgSafeStr = function(variable) {
	if(variable) {
		return variable;
	}
	else {
		return '';
	}
}
