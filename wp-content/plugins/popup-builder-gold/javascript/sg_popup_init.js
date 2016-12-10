function SgPopupInit(popupData) {
	this.popupData = popupData;
	this.shortcodeInPopupContent();
	this.cloneToHtmlPopup();
}

SgPopupInit.prototype.cloneToHtmlPopup = function() {
	var currentPopupId = this.popupData['id'];

	/*When content does not have shortcode*/
	if(jQuery("#sgpb-all-content-"+currentPopupId).length == 0) {
		return;
	}
	
	jQuery('.sg-current-popup-'+currentPopupId).append(this.decodeBase64(jQuery("#sgpb-all-content-"+currentPopupId).html()));

	this.popupResizing(currentPopupId);
	jQuery('#sgcolorbox').bind('sgPopupCleanup', function() {
		jQuery('#sgpb-all-content-'+currentPopupId).appendTo(jQuery("#sg-popup-content-"+currentPopupId));
	});
}

SgPopupInit.prototype.decodeBase64 = function(s) {
    if(typeof window.atob == 'function') {
        return decodeURIComponent(escape(window.atob(s)));
    }

    var e={},i,b=0,c,x,l=0,a,r='',w=String.fromCharCode,L=s.length;
    var A="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    for(i=0;i<64;i++){e[A.charAt(i)]=i;}
    for(x=0;x<L;x++){
        c=e[s.charAt(x)];b=(b<<6)+c;l+=6;
        while(l>=8){((a=(b>>>(l-=8))&0xff)||(x<(L-2)))&&(r+=w(a));}
    }
    return r;
}

SgPopupInit.prototype.popupResizing = function(currentPopupId) {

	var width = this.popupData['width'];
	var height = this.popupData['height'];
	var maxWidth = this.popupData['maxWidth'];
	var maxHeight = this.popupData['maxHeight'];

	if(maxWidth == '' && maxHeight == '') {
		jQuery.sgcolorbox.resize({'width': width, 'height': height});
	}
}

SgPopupInit.prototype.shortcodeInPopupContent = function() {

	jQuery(".sg-show-popup").bind('click',function() {
		var sgPopupID = jQuery(this).attr("data-sgpopupid");
		var sgInsidePopup = jQuery(this).attr("insidepopup");

		if(typeof sgInsidePopup == 'undefined' || sgInsidePopup != 'on') {
			return false;
		}
		
		jQuery.sgcolorbox.close();
		
		jQuery('#sgcolorbox').bind("sgPopupClose", function() {
			if(sgPopupID == '') {
				return;
			}
			sgPoupFrontendObj = new SGPopup();
			sgPoupFrontendObj.showPopup(sgPopupID,false);
			sgPopupID = '';
		});
	});
}

SgPopupInit.prototype.overallInit = function() {
	jQuery('.sg-popup-close').bind('click', function() {
		jQuery.sgcolorbox.close();
	});

	//Facebook reInit
	if(jQuery('#sg-facebook-like').length && typeof FB !== 'undefined') {
		FB.XFBML.parse();
	}
}

SgPopupInit.prototype.initByPopupType = function() {
	var data = this.popupData;
	var popupObj = {};
	var popupType = data['type'];

	switch(popupType) {
		case 'countdown':
			var popupObj = new SGCountdown();
			popupObj.init();
			break;
		case 'contactForm':
			popupObj = new SgContactForm();
			popupObj.buildStyle();
			break;
		case 'social':
			popupObj = new SgSocialFront();
			popupObj.init();
			break;
		case 'subscription':
			popupObj = new SgSubscription();
			popupObj.init();
			break;
		case 'ageRestriction':
			popupObj = new SGAgeRestriction();
			popupObj.init();
			break;
	}

	return popupObj;
}