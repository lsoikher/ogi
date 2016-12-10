function SGExitIntnetPopup() {
	this.exitIntntType;
	this.expireTime;
	this.sgPopupObj = new SGPopup();
	this.alertText;
}

SGExitIntnetPopup.prototype.setType = function(type) {
	this.exitIntntType = type;
}

SGExitIntnetPopup.prototype.getType = function() {
	return this.exitIntntType;
}

SGExitIntnetPopup.prototype.setExpireTime = function(time) {
	this.expireTime = time;
}

SGExitIntnetPopup.prototype.getExpireTime = function() {
	return this.expireTime;
}

SGExitIntnetPopup.prototype.setAlertText = function(text) {
	this.alertText = text;
}

SGExitIntnetPopup.prototype.getAlertText = function() {
	return this.alertText;
}

SGExitIntnetPopup.prototype.buildExitIntent = function(id) {
	var type = this.getType();
	var that = this;

	if(type == "soft") {
		this.softMode(id);
	}
	else if(type == "aggressive") {
		this.aggressiveMode(id);
	}
	else if(type == "softAndAgressive") {
		this.softAndAgressiveMode(id);
	}
	else if(type == "agresiveWithoutPopup") {
		this.aggressiveMode(id);
	}
}

SGExitIntnetPopup.prototype.softMode = function(id) {
	var that = this;
	 document.addEventListener("mouseout", function(event) {
		if (event.toElement == null && event.relatedTarget == null) {
			var result = that.canOpen(id, 'soft');
			if(result){
				return;
			}
			if (jQuery("#sgcolorbox").css("display") !== "block") { /* Check colorbox is open */
				that.sgPopupObj.showPopup(id, false);
			}
		 }
	 });
}

SGExitIntnetPopup.prototype.aggressiveMode = function(id) {
	var that = this;
	sgAddEvent(window, "beforeunload", function (e) {
		var result = that.canOpen(id, 'aggressive');
		if(result){
			return;
		}
		(e || window.event).returnValue = that.triggerOpenPopup(id);
		e.returnValue = that.triggerOpenPopup(id);
	});
}

SGExitIntnetPopup.prototype.softAndAgressiveMode = function(id) {
	this.softMode(id);
	this.aggressiveMode(id);
}

SGExitIntnetPopup.prototype.triggerOpenPopup = function(id) {
	if(this.getType() !== 'agresiveWithoutPopup') {
		this.sgPopupObj.showPopup(id, false);
	}
	return this.getAlertText();
}

SGExitIntnetPopup.prototype.canOpen = function(id, type) {

	if(!jQuery.cookie('SGExitIntnetPopup'+id+type)) {
		this.setCookies(id, type);
		return false;
	}
	return true;
}

SGExitIntnetPopup.prototype.setCookies = function(id, type) {
	var that = this;
	var date = new Date();
	var minutes = this.getExpireTime();
	date.setTime(date.getTime() + (minutes * 60 * 1000));

	jQuery(document).ready(function() {
		jQuery('#sgcolorbox').on('sgPopupClose', function(e) {
			if(that.getExpireTime() !== 'always') {
			jQuery.cookie('SGExitIntnetPopup'+id+type,id, { expires: date});
			if(that.getExpireTime() == 'perSesion') {
				jQuery.cookie('SGExitIntnetPopup'+id,id);
			}
		}
		});
	});
};

SGExitIntnetPopup.prototype.init = function(id) {
	var data = SG_POPUP_DATA[id];
	var exitIntentOptions = JSON.parse(data['exitIntentOptions']);
	var exitIntentType = exitIntentOptions['exit-intent-type'];
	var expireTime = exitIntentOptions['exit-intent-expire-time'];
	var exitIntentAlert = exitIntentOptions['exit-intent-alert'];
	this.setExpireTime(expireTime);
	this.setType(exitIntentType);
	this.setAlertText(exitIntentAlert);
	this.buildExitIntent(id);
}

