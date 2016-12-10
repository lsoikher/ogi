function SGCountdown()
{
	this.interval;
	this.content = ' ';
	this.closeType = 'on';
	this.countdownType;
	this.showSeconds = false;
	this.clockFace = 'DailyCounter';
	this.language = 'English';
	this.sgClock = '';
}

SGCountdown.prototype.setInterval = function(interval)
{
	this.interval = interval;
}

SGCountdown.prototype.setContent = function(content)
{
	this.content = content;
}

SGCountdown.prototype.setCloseType = function(closeType)
{
	this.closeType = closeType;
}

SGCountdown.prototype.setCountdownType = function(countdownType)
{
	this.countdownType = countdownType;
}

SGCountdown.prototype.setLanguage = function(language)
{
	this.language = language;
}

SGCountdown.prototype.setClock = function(clock)
{
	this.sgClock = clock;
}

SGCountdown.prototype.getClock =  function()
{
	return this.sgClock;
}

SGCountdown.prototype.sgClockFace = function(countType)
{
	countType = Number(countType);
	var that = this;
	switch(countType) {
		case 1:
		that.clockFace = 'DailyCounter'
		that.showSeconds = true;
		break;
		case 2:
		that.clockFace = 'DailyCounter';
		that.showSeconds = false;
		break;
	}

}

SGCountdown.prototype.render = function()
{
	var that = this;
	var clock;
	this.sgClockFace(this.countdownType);
	clock = FlipClock(jQuery('.sg-counts-content'), this.interval,{
		clockFace: that.clockFace,
		autoStart: true,
		countdown: true,
		showSeconds: this.showSeconds,
		language: this.language,
		callbacks: {
			start: function() {
			},
			stop: function() {

				if(SgCountdownParams.countdownAutoclose) {
					
					if(typeof jQuery.sgcolorbox != "undefined") {
						jQuery.sgcolorbox.close();	
					}
				}
			}
		}
	});
	this.setClock(clock);
}

SGCountdown.prototype.settingsColorbox = function()
{
	SG_POPUP_SETTINGS.closeButton = true;
	SG_POPUP_SETTINGS.escKey = true;
	SG_POPUP_SETTINGS.overlayClose = true;
	SG_POPUP_SETTINGS.html = this.content;
	return SG_POPUP_SETTINGS;
}

SGCountdown.prototype.adminInit = function() {
	var that = this;
	jQuery("[name = 'counts-language']").bind("change", function() {
		that.setLanguage(jQuery(this).val());
		that.render();
	});
	jQuery("[name = 'sg-countdown-type']").bind("change", function() {
		that.setCountdownType(jQuery(this).val());
		that.render();
	});
	sgCountdownCalendar.bind("change", function() {
		var sgDueDateTime = jQuery(this).val()+":00";

		var timeNow = Math.floor(new Date().getTime() / 1000);
		var seconds = Math.floor(new Date(sgDueDateTime).getTime() / 1000) - timeNow;
		if(seconds < 0) {
			seconds = 0;
		}
		that.setInterval(seconds);
		that.render();
	});
	this.trigger();
	this.render();
}

SGCountdown.prototype.trigger = function() {
	jQuery("[name = 'counts-language']").trigger("change");
	jQuery("[name = 'sg-countdown-type']").trigger("change");
	sgCountdownCalendar.trigger("change");
}

SGCountdown.prototype.init =  function()
{

	this.setInterval(SgCountdownParams.seconds);
	this.setCountdownType(SgCountdownParams.type);
	this.setLanguage(SgCountdownParams.countLanguage);
	this.render();
}
