jQuery(document).ready(function() {
	// remember hidden option, and act accordingly
	if(fsmlReadCookie('fsmlOpen')=='closed'){
		jQuery('#fsml_ff').hide(0, function() { jQuery('#fsml_ffhidden').show(0); });
	}
});

function fsmlHide(an,uc,cl){
	switch(an){
		case 'fade': jQuery('#fsml_fblikemodal').fadeOut(300); jQuery('#fsml_ff').fadeOut(600, function() { jQuery('#fsml_ffhidden').fadeIn(400); }); break; 
		case 'none': jQuery('#fsml_fblikemodal').hide(); jQuery('#fsml_ff').hide(); jQuery('#fsml_ffhidden').show(); break; 
		default: jQuery('#fsml_fblikemodal').hide(300); jQuery('#fsml_ff').hide(600, function() { jQuery('#fsml_ffhidden').fadeIn(400); }); break; 
	}
	if(uc)
		fsmlCreateCookie('fsmlOpen','closed',cl);
}

function fsmlShow(an){
	switch(an){
		case 'fade': jQuery('#fsml_ff').fadeIn(1000); jQuery('#fsml_ffhidden').fadeOut(500); break;
		case 'none': jQuery('#fsml_ff').show(); jQuery('#fsml_ffhidden').hide(); break;
		default: jQuery('#fsml_ff').show(1000); jQuery('#fsml_ffhidden').hide(500); break;
	}
	fsmlEraseCookie('fsmlOpen'); //continue to clear cookies on show regardless of cookies setting
}

function fsmlCreateCookie(name,value,days) {
	if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function fsmlReadCookie(name){
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function fsmlEraseCookie(name){
	fsmlCreateCookie(name,"",-1);
}