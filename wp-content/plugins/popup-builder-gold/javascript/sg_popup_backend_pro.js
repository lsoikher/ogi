function backendPro() {

}

backendPro.prototype.dataImport = function() {

	var custom_uploader;
	jQuery('#js-upload-export-file').click(function(e) {
		e.preventDefault();

		/* If the uploader object has already been created, reopen the dialog */
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}

		/* Extend the wp.media object */
		custom_uploader = wp.media.frames.file_frame = wp.media({
			titleFF: 'Select Export File',
			button: {
				text: 'Select Export File'
			},
			multiple: false,
			library : { type  :  'text/plain'},
		});
		/* When a file is selected, grab the URL and set it as the text field's value */
		custom_uploader.on('select', function() {
			attachment = custom_uploader.state().get('selection').first().toJSON();

			var data = {
				action: 'import_popups',
				attachmentUrl: attachment.url
			}
			jQuery(".js-sg-import-gif").removeClass("sg-hide-element");
			jQuery.post(ajaxurl, data, function(response,d) {
				location.reload();
				jQuery(".js-sg-import-gif").addClass("sg-hide-element");
			});
		});
		/* Open the uploader dialog */
		custom_uploader.open();
	});

}

backendPro.prototype.addSelectboxValuesIntoInput = function() {
	
	var selectedPages = [];
	var selectedPosts = [];
	 
	jQuery("#add-form").submit(function(e) {
		jQuery(".js-sg-selected-pages").val();
		jQuery(".js-sg-selected-posts").val();

		var pages = jQuery("select[data-slectbox='all-selected-page'] > option:selected");
		var posts = jQuery("select[data-slectbox='all-selected-posts'] > option:selected");
		for(i=0; i<pages.length; i++) {
			selectedPages.push(pages[i].value);
		}
		for(i=0; i<posts.length; i++) {
			selectedPosts.push(posts[i].value);
		}
		jQuery(".js-sg-selected-posts").val(selectedPosts);
		jQuery(".js-sg-selected-pages").val(selectedPages);
	});
}

backendPro.prototype.lazyLoading = function() {
	var that = this;

	jQuery("input[value='selected']:checked").each(function() {
		that.prepareToAjax(jQuery(this));
	});

	jQuery("input[value='selected']").bind("change",function() {
		that.prepareToAjax(jQuery(this));
	});

	jQuery(".js-multiselect").scroll(function(e) {
		var elem = jQuery(e.currentTarget);
	    if (elem[0].scrollHeight - elem.scrollTop() <= elem.outerHeight()) {
	    	var name = jQuery(this).attr("data-sorce");
	    	dataInput = jQuery("[name="+name+"][value='selected']");
	        that.prepareToAjax(dataInput);
	    }
	});


}
backendPro.prototype.prepareToAjax = function(dataInput) {

	if(dataInput.length != 1) {
		return false;
	}

	var popupId = dataInput.attr("data-popupid");
	var selectboxClass = dataInput.attr("data-selectbox-role");
	var selectBoxSelector = dataInput.attr("data-selectbox-role");
	var postType = dataInput.attr("data-name");
	var loadingNumber = dataInput.attr("data-loading-number");
	
	var selectBoxData = {
		popupId: popupId,
		selectboxClass: selectboxClass,
		postType: postType,
		selectBoxSelector: selectBoxSelector,
		loadingNumber: loadingNumber,
		dataInput: dataInput
	};

	this.lazyLoadViaAjax(selectBoxData);
}

backendPro.prototype.lazyLoadViaAjax = function(selectBoxData) {

	var selectboxClass = selectBoxData['selectBoxSelector'];

	var data = {
		action: 'lazy_loading',
		popupId: selectBoxData['popupId'],
		postType: selectBoxData['postType'],
		loadingNumber: selectBoxData['loadingNumber'],
		beforeSend: function() {
			jQuery('.spiner-'+selectBoxData['postType']).removeClass("sg-hide-element");
		},
	}
	
	jQuery.post(ajaxurl, data, function(response,d) {
		
		selectBoxData['dataInput'].removeAttr("data-loading-number");
		selectBoxData['dataInput'].attr("data-loading-number",++selectBoxData['loadingNumber']);
		jQuery("."+selectboxClass).append(response);
		jQuery("."+selectboxClass).nextAll(".js-sg-spinner").addClass("sg-hide-element");
	});
}

backendPro.prototype.timepicker = function() {
	if(jQuery('.sg-time-picker').length == 0) return;
	jQuery('.sg-time-picker').datetimepicker({
		datepicker:false,
		format:'H:i'
	});
}

backendPro.prototype.addToSubscribers = function() {
	var that = this;

	jQuery(".sg-add-to-list-button").bind('click', function() {
		var susbEmail = jQuery(".add-subs-email").val();
		var subsFirstName = jQuery(".subs-first-name").val();
		var subsLastName = jQuery(".subs-last-name").val();
		var listSubscriptonType = [];

		jQuery(".js-sg-newslatter-forms > option").each(function() {			
			if(jQuery(this).prop("selected")) {
				listSubscriptonType.push(jQuery(this).val());
			}
		});
		var validateEmail = susbEmail.search(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/);
		
		if(validateEmail == -1) {
			jQuery(".sg-email-error").removeClass("sg-hide-element");
			return;
		}
		jQuery(".sg-email-error").addClass("sg-hide-element");
		var data = {
			action: 'add_to_subsribers',
			firstName: subsFirstName,
			lastName: subsLastName,
			email: susbEmail,
			subsType: listSubscriptonType,
			beforeSend: function() {
				jQuery(".js-sg-spinner").removeClass("sg-hide-element");
			}
		}
		
		that.addToSubscribersViaAjax(data);
	});
}

backendPro.prototype.addToSubscribersViaAjax = function(data) {

	
	jQuery.post(ajaxurl, data, function(response,d) {
		
		jQuery(".js-sg-spinner").addClass("sg-hide-element");
		jQuery(".sg-successfully").removeClass("sg-hide-element");
	});
}

backendPro.prototype.fixSublsrcitionBulkCheckbox = function() {
	jQuery('#bulk,.column-bulk').removeClass().addClass('manage-column column-cb check-column');
}

backendPro.prototype.toggleCheckedSubsribers = function() {
	var that = this;
	jQuery('.subs-bulk').each(function() {
		jQuery(this).bind('click', function() {
			var bulkStatus = jQuery(this).prop("checked");
			that.changeCheckedSubscribers(bulkStatus);
		});
	});
}

backendPro.prototype.changeCheckedSubscribers = function(bulkSTatus) {

	jQuery('.subs-delete-checkbox').each(function() {
		jQuery(this).prop( "checked", bulkSTatus );
	})
}

backendPro.prototype.deleteSubscribers = function() {
	var checkedSubscribersList = [];
	var that = this;
	jQuery('.sg-subs-delete-button').bind('click', function() {
		var isSure = confirm('Are you sure?');

		if(!isSure) {
			return;
		}
		jQuery('.subs-delete-checkbox').each(function() {
			var isChecked = jQuery(this).prop('checked');
			if(isChecked) {
				var subscriberId = jQuery(this).attr('data-delete-id');
				checkedSubscribersList.push(subscriberId);
			}
		});
		if(checkedSubscribersList.length == 0) {
			alert('Please select at least one subscriber.');
		}
		else {
			that.deleteSbubsribersViaAjax(checkedSubscribersList);
		}
	})
}

backendPro.prototype.deleteSbubsribersViaAjax = function(checkedSubscribersList) {
	
	var data = {
		action: 'subsribers_delete',
		subsribersId: checkedSubscribersList,
		beforeSend: function() {
			jQuery('.spiner-subscribers').removeClass("sg-hide-element");
		},
	}

	jQuery.post(ajaxurl, data, function(response,d) {
		jQuery('.spiner-subscribers').addClass("sg-hide-element");
		window.location.reload();
	});	
}

backendPro.prototype.sendNeswsletter = function() {
	var that = this;

	jQuery('.sg-newsletter-sumbit').bind('click',function() {
		var subsFormType = jQuery('.js-sg-newslatter-forms option:selected').val();
		var emailsOneTime = jQuery('.sg-emails-in-flow').val();
		var newsletterSubject = jQuery('.sg-newsletter-subject').val();
		var messageBody = jQuery("#sg_newsletter_text").val();

		var NewsLatterData = {
			'subsFormType': subsFormType,
			'emailsOneTime': emailsOneTime,
			'newsletterSubject': newsletterSubject,
			'messageBody': messageBody
		}
		that.sendNewsletterViaAjax(NewsLatterData);
	});
}

backendPro.prototype.sendNewsletterViaAjax = function(NewsLatterData) {

	var data = {
		action: 'send_newsletter',
		NewsLatterData: NewsLatterData,
		beforeSend: function() {
			jQuery(".js-sg-send-subsribe").removeClass('sg-hide-element');
		}
	}
	jQuery.post(ajaxurl, data, function(response,d) {
		jQuery(".js-sg-send-subsribe").addClass('sg-hide-element');
		jQuery(".sgpb-newsletter-notice").removeClass('sg-hide-element');
	});	 
}

backendPro.prototype.addSubscriptionReload = function() {
	jQuery(".sg-close").bind('click', function() {
		window.location.reload();
	});
}

backendPro.prototype.sgDownloadSubsErrorLogs = function() {
	jQuery(".js-sg-newslatter-forms").change(function() {
	
		var subsType = jQuery(this,'option').val();
		var data = {
			action: 'subs_error_log_count',
			subsType: subsType
		}
		jQuery.post(ajaxurl, data, function(countErrorLogs,d) {
			if(countErrorLogs != 0) {
				jQuery(".sg-subs-error-list").attr("data-subs-list", subsType);
				jQuery(".sg-subs-error-list").removeClass("sg-hide-element");
			}
			else {
				jQuery(".sg-subs-error-list").attr("data-subs-list",'');
				jQuery(".sg-subs-error-list").addClass("sg-hide-element");
			}
		});
	});
	jQuery(".js-sg-newslatter-forms").trigger("change");
}

backendPro.prototype.init = function() {
	this.dataImport();
	this.lazyLoading();
	this.addSelectboxValuesIntoInput();
	this.addToSubscribers();
	this.timepicker();
	this.fixSublsrcitionBulkCheckbox();
	this.toggleCheckedSubsribers();
	this.deleteSubscribers();
	this.sendNeswsletter();
	this.addSubscriptionReload();
	this.sgDownloadSubsErrorLogs();
}

jQuery(document).ready(function() {
	var proObj = new backendPro();
	proObj.init();
})