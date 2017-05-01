jQuery(document).ready(function(){
	jQuery('#jstest').hide(); // if this fails, an error is displayed
	loadTabs();
	
	// prepare the color pickers (for the appearance sub-page)
	// no longer supporting color pickers for pre-3.5
	jQuery('.fsml-color-picker').wpColorPicker();
	
	// 3.5+ media/image upload (no longer supporting older versions)
	// File uploader
	var file_frame;
	var formfield;
	jQuery('.img_upload_button').on('click', function( event ){
		formfield = jQuery(this).prev().attr('id');

		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: 'Select an Image',
		  button: {
			text: 'Insert Image',
		  },
		  library: {
			type: 'image'
		  },
		  multiple: false  // only allow the one file to be selected
		});
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').first().toJSON();
		  jQuery('#'+formfield).attr('value',attachment.url);
		  jQuery('#'+formfield+'_preview').attr('src',attachment.url);
		});
		// Finally, open the modal
		file_frame.open();
		
		//add custom images to the dialog
		//jQuery('.media-modal .media-modal-content .attachments-browser ul.attachments').append(html);
	});
	

	// hide the custom colorscheme options when not using a custom colorscheme
	jQuery('#fsmlcscustom').click(function(){ jQuery('#fsml_custom_colors').show(500); });
	jQuery('#fsmlcslight').click(function(){ jQuery('#fsml_custom_colors').hide(500); });
	jQuery('#fsmlcsdark').click(function(){ jQuery('#fsml_custom_colors').hide(500); });

	// hide the custom border color option when there is no border
	jQuery('#borderyes').click(function(){ jQuery('#bordercolor').show(300); });
	jQuery('#borderno').click(function(){ jQuery('#bordercolor').hide(300); });

	// hide the custom shadow color option when there is no border
	jQuery('#shadowyes').click(function(){ jQuery('#shadowcolor').show(300); });
	jQuery('#shadowno').click(function(){ jQuery('#shadowcolor').hide(300); });

	// dynamically show the correct size options based on whether the site is running fixed or dynamic width (also hide the border option since no border is allowed with dynamic widths (since border can't be %)
	jQuery('#fixedwidth').click(function(){ jQuery('#fixedsize').show(); jQuery('#dynamicsize').hide(); jQuery('#showborder').show(); });
	jQuery('#dynamicwidth').click(function(){ jQuery('#fixedsize').hide(); jQuery('#dynamicsize').show(); jQuery('#showborder').hide(); jQuery('#bordercolor').hide(); });
	
	//dynamically show options based on availability/relevance
	jQuery('#noshow').click(function(){ jQuery('#expandtitle').hide(300); jQuery('#hsanimation').show(300); });
	jQuery('#nohide').click(function(){ jQuery('#expandtitle').hide(300); jQuery('#hsanimation').hide(300); });
	jQuery('#shyes').click(function(){ jQuery('#expandtitle').show(300); jQuery('#hsanimation').show(300); });
	jQuery('#sthidden').click(function(){ jQuery('#expandtitle').show(300); jQuery('#hsanimation').show(300); });
	
	// toggle sub-settings for advanced general options
	jQuery('#hidesmall').bind('change', function () {
		jQuery('#metaviewport').toggle(300);
	});
	jQuery('#usecookies').bind('change', function () {
		jQuery('#cookielength').toggle(300);
	});
	
	//show/hide options specific to certain action buttons
	jQuery('#actionfblike').bind('change', function () {
		jQuery('#fburltolike').toggle(300);
	});
	jQuery('#actionytsub').bind('change', function () {
		jQuery('#ytuseridetc').toggle(300);
	});

	//show/hide advanced format options
	jQuery('#showapadvanced').click(function(){ jQuery('.apadvanced').show(300); jQuery('#hideapadvanced').show(300); jQuery(this).hide(200); jQuery('#apadvanced').val('shown'); });
	jQuery('#hideapadvanced').click(function(){ jQuery('.apadvanced').hide(300); jQuery('#showapadvanced').show(300); jQuery(this).hide(200); jQuery('#apadvanced').val('hidden'); });

	//design/themes
	//themes vs. custom
	jQuery('#theme-customize').click(function(){ jQuery('.dadvanced').show(300); jQuery('#themes').hide(800); jQuery('#currenttheme').val('custom'); });
	jQuery('#tobuiltin-theme').click(function(){ jQuery('.dadvanced').hide(300); jQuery('#themes').show(300); });
	
	//show/hide advanced general options
	jQuery('#showgenadvanced').click(function(){ jQuery('.genadvanced').show(300); jQuery('#hidegenadvanced').show(300); jQuery(this).hide(200); jQuery('#genadvanced').val('shown'); });
	jQuery('#hidegenadvanced').click(function(){ jQuery('.genadvanced').hide(300); jQuery('#showgenadvanced').show(300); jQuery(this).hide(200); jQuery('#genadvanced').val('hidden'); });
	
} );

function switchTabs( tabto ){
	jQuery('#fsmlopform table.form-table').hide(300);
	jQuery('#fsml_'+tabto).show(300);
	jQuery('#fsmlsubpages .fsmlcurrent').removeClass( 'fsmlcurrent' );
	jQuery('#fsmlsuboptionspage_'+tabto).addClass( 'fsmlcurrent' );
	jQuery('#settingstab').val(tabto);
}

function setTheme( theme ){
	// change the theme interface shell
	jQuery('#current-theme').val(theme);
	jQuery('.selected-theme').removeClass('selected-theme');
	jQuery('#theme-'+theme).addClass('selected-theme');
	
	// theme settings are defined here
	switch(theme){
	// colorscheme, grayscale, frame mouseover effect, icon mouseover effect, border radius, shadow
		case 'bright': setThemeOptions( 'light', 0, 'no', 'yes', 'none', 'no' ); break;
		case 'dark': setThemeOptions( 'dark', 1, 'yes', 'yes', 'none', 'no' ); break;
		case 'elegant': setThemeOptions( 'light', 1, 'yes', 'yes', 'none', 'no' ); break;
		case 'subtle': setThemeOptions( 'light', 0, 'yes', 'yes', 'med', 'yes' ); break;
		case 'contrast': setThemeOptions( 'dark', 0, 'no', 'no', 'med', 'yes' ); break;
	}
}

function setThemeOptions( colorscheme, grayscale, framehover, iconhover, borderrad, shadow ){
	// the meat: set the options
	jQuery('input:radio[name="fsml_options[colorscheme]"]').val([colorscheme]);
	if(colorscheme != 'custom')
		jQuery('#fsml_custom_colors').hide();
	jQuery('input[name="fsml_options[grayscaleicons]"]').prop('checked',grayscale);
	jQuery('input:radio[name="fsml_options[framehovereffect]"]').val([framehover]);
	jQuery('input:radio[name="fsml_options[hovereffect]"]').val([iconhover]);
	jQuery('input:radio[name="fsml_options[borderradius]"]').val([borderrad]);
	jQuery('input:radio[name="fsml_options[shadow]"]').val([shadow]);
	jQuery('input:radio[name="fsml_options[border]"]').val(['no']); // none of the built-in themes recommend using borders
}

function submitSiteDone(){
	jQuery('#sitesubmitted').val('yes');
}