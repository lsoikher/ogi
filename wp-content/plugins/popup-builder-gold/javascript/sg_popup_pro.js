SGPopup.prototype.canOpenOnce = function(id) {
	if(typeof jQuery.cookie('sgPopupDetails'+id) == "undefined") {
		return true;
	}
	var cookieData = JSON.parse(jQuery.cookie('sgPopupDetails'+id));

	if(cookieData.popupId == id && cookieData.openCounter >= this.numberLimit) {
		return false;
	}
	else {
		return true
	}
	
}

SGPopup.prototype.cantPopupClose = function() {
	this.popupEscKey  = false;
	this.popupCloseButton = false;
	this.popupOverlayClose = false;
	this.popupContentClick = false;
}

SGPopup.prototype.forMobile = function() {
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
 		return true;
	}
	return false;
}

SGPopup.inactivityIdicator = 0;

SGPopup.prototype.showPopupAfterInactivity = function(popupId) {
	var data = SG_POPUP_DATA[popupId];
	var that = this;
	/*1sec = 1000mic*/
	var inactivityTimout = data['inactivity-timout']+"000";
	var idleInterval = setInterval(function() {that.timerIncrement(popupId, idleInterval)}, inactivityTimout);
	
    jQuery(window).mousemove(function (e) {
		SGPopup.inactivityIdicator++;
    });
    jQuery(window).keypress(function (e) {
		SGPopup.inactivityIdicator++;
    });
}

SGPopup.prototype.timerIncrement = function(popupId , idleInterval) {
	var lastActivity = SGPopup.inactivityIdicator;

	if(lastActivity == 0) {
		clearInterval(idleInterval);
		this.showPopup(popupId, true);
	}
	SGPopup.inactivityIdicator = 0;
}

SGPopup.prototype.onScrolling = function(popupId) {
	that = this;
	var scrollStatus = false;
	jQuery(window).on('scroll', function(){
		var scrollTop = jQuery(window).scrollTop();
		var docHeight = jQuery(document).height();
		var winHeight = jQuery(window).height();
		var scrollPercent = (scrollTop) / (docHeight - winHeight);
		var scrollPercentRounded = Math.round(scrollPercent*100);
		if(beforeScrolingPrsent < scrollPercentRounded) {
			if(scrollStatus == false) {
				that.showPopup(popupId,true);
				scrollStatus = true;
			}
		}
	});
}

SGPopup.prototype.removeCookie = function(openOnce) {
	if (openOnce === false) {
		//jQuery.removeCookie("sgPopupDetails");
	}
}

SGPopup.prototype.proInit = function() {
	var that = this;
	
	jQuery('#sgcolorbox').on('sgColorboxOnCompleate',function(e) {

		if(arguments[1] == 'on') { /* push to bottom param */
			jQuery('#sgcboxLoadedContent').css({'position': 'relative'});
		}
		
		/* For AgeRestcion and Social popups*/
		that.isPushToBottom();
	});
}

SGPopup.prototype.disablePopupOverlay = function() {
	jQuery('#sgcolorbox').on("sgColorboxOnOpen", function() {
		jQuery("#sgcboxOverlay").remove();
	});
}

SGPopup.prototype.autoClosePopup = function() {
	jQuery.sgcolorbox.close();
}

SGPopup.prototype.isPushToBottom = function() {

	var loadedContent = document.getElementById('sgcboxLoadedContent');
	var loadConetnetHasScroll = loadedContent.scrollHeight>loadedContent.clientHeight;
	
	if(jQuery(".js-sg-push-on-bottom").length != 0 && loadConetnetHasScroll) {
		jQuery(".js-sg-push-on-bottom").removeClass("sg-push-to-bottom");
	}
}

SGPopup.prototype.sgPopupShouldOpen = function(popupId) {

	if(typeof SG_POPUP_DATA[popupId] == "undefined") {
		return false;
	}
	
	var openMobile = SG_POPUP_DATA[popupId]['openMobile']; /* on or '' */
	var hideForMobile = SG_POPUP_DATA[popupId]['forMobile']; /* on or '' */
	var popupThisOften = SG_POPUP_DATA[popupId]['repeatPopup'];
	var popupType = SG_POPUP_DATA[popupId]['type'];
	var popupNumberLimit = SG_POPUP_DATA[popupId]['popup-appear-number-limit']

	var isMobile = this.forMobile(); //if fasle it's mean not mobile

	/*if not show in desktop */
	if(isMobile == false && openMobile) {
		return false;
	}
	if(isMobile == true && hideForMobile) {
		return false;
	}
	if(popupThisOften) {
		this.numberLimit = popupNumberLimit;
		var canOpen = this.canOpenOnce(popupId);
		if(!canOpen) {
			return false;
		}
	}
	if(popupType == 'exitIntent') {
		return false;
	}

	return true;
}

SGPopup.prototype.popupOpenOnWindowLoad = function(popupId) { 
	sgAddEvent(window, 'load', this.popupOpenById(popupId));
}

jQuery(document).ready(function($) {
	var popupObj = new SGPopup();
	popupObj.proInit();
	var that = this;
});