function SgPickers() {

}

SgPickers.prototype.init = function() {
	jQuery(".sg-calndar").addClass("input-width-static");

	jQuery('.sg-calndar').bind("click",function() {
		jQuery("#ui-datepicker-div").css({'z-index': 9999});
	});
}

SgPickers.prototype.datepicker = function() {
	var that = this;

	sgCountdownCalendar = jQuery('#sg-datapicker').datetimepicker({
		format:'M d y H:i',
		minDate: 0
	});
}

jQuery(document).ready(function($){

	pickersObj = new SgPickers();
	pickersObj.init();
	pickersObj.datepicker();
});