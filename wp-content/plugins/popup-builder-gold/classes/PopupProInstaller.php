<?php
class PopupProInstaller
{

	public static function createTables($blogsId)
	{
		global $wpdb;
		$createTableStr = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogsId;

		$sgPopupIframeBase = $createTableStr."sg_iframe_popup (
				`id` int(12) NOT NULL,
				`url` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$sgVideoPopupBase = $createTableStr."sg_video_popup (
				`id` int(12) NOT NULL,
				`url` text NOT NULL,
				`real_url` text NOT NULL,
				`options` TEXT NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgAgeRestrictionPopupBase = $createTableStr."sg_age_restriction_popup (
				`id` int(12) NOT NULL,
				`content` TEXT NOT NULL,
				`yesButton` varchar(255) NOT NULL,
				`noButton` varchar(255) NOT NULL,
				`url` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgCountdownPopupBase = $createTableStr."sg_countdown_popup (
				`id` int(12) NOT NULL,
				`content` TEXT NOT NULL,
				`options` TEXT NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgSocialPopupBase = $createTableStr."sg_social_popup (
				`id` int(12) NOT NULL,
				`socialContent` text NOT NULL,
				`buttons` TEXT NOT NULL,
				`socialOptions` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgExitIntentBase = $createTableStr."sg_exit_intent_popup (
				`id` int(12) NOT NULL,
				`content` TEXT NOT NULL,
				`options` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$sgSubscriptionBase = $createTableStr."sg_subscription_popup (
				`id` int(12) NOT NULL,
				`content` TEXT NOT NULL,
				`options` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgSubscribersBase = $createTableStr."sg_subscribers (
				`id` int(12) NOT NULL AUTO_INCREMENT,
				`firstName` varchar(255),
				`lastName` varchar(255),
				`email` varchar(255),
				`subscriptionType` varchar(255),
				`status` varchar(255),
				PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgSubsErrorLog = $createTableStr."sg_subscription_error_log (
				`id` int(12) NOT NULL AUTO_INCREMENT,
				`popupType` varchar(255),
				`email` varchar(255),
				`date` varchar(255),
				PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgContactFormBase = $createTableStr."sg_contact_form_popup (
				`id` int(12) NOT NULL AUTO_INCREMENT,
				`content` text,
				`options` text,
				PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$sgPopupInPages = $createTableStr."sg_popup_in_pages (
			`id` int(12) NOT NULL AUTO_INCREMENT,
			`popupId` int(12) DEFAULT NULL,
			`pageId` int(12) DEFAULT NULL,
			`type` varchar(255) DEFAULT NULL, 
			PRIMARY KEY (`id`),
			UNIQUE KEY `ppopup_pages` (`popupId`,`pageId`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";

		$wpdb->query($sgPopupIframeBase);
		$wpdb->query($sgVideoPopupBase);
		$wpdb->query($sgAgeRestrictionPopupBase);
		$wpdb->query($sgCountdownPopupBase);
		$wpdb->query($sgSocialPopupBase);
		$wpdb->query($sgExitIntentBase);
		$wpdb->query($sgSubscriptionBase);
		$wpdb->query($sgSubscribersBase);
		$wpdb->query($sgSubsErrorLog);
		$wpdb->query($sgContactFormBase);
		$wpdb->query($sgPopupInPages);
	}

	public static function install()
	{
		global $wpdb;

		$obj = new self();
		$obj->createTables('');

		if (is_multisite()) {
			$sites = wp_get_sites();
			foreach($sites as $site) {
				$blogsId = $site['blog_id']."_";
				global $wpdb;
				$obj->createTables($blogsId);
			}
		}
	}

	public function uninstallTables($blogId)
	{
		global $wpdb;

		$popupIframeTable = $wpdb->prefix.$blogId."sg_iframe_popup";
		$popupIframeSql = "DROP TABLE ". $popupIframeTable;

		$popupVideoTable = $wpdb->prefix.$blogId."sg_video_popup";
		$popupVideoSql = "DROP TABLE ". $popupVideoTable;

		$popupAgeRestrictionTable = $wpdb->prefix.$blogId."sg_age_restriction_popup";
		$popupAgeRestrictionSql = "DROP TABLE ". $popupAgeRestrictionTable;

		$popupCountdownTable = $wpdb->prefix.$blogId."sg_countdown_popup";
		$popupCountdownSql = "DROP TABLE ". $popupCountdownTable;

		$popupSocialTable = $wpdb->prefix.$blogId."sg_social_popup";
		$popupSocialSql = "DROP TABLE ". $popupSocialTable;

		$sgExitIntentTable = $wpdb->prefix.$blogId."sg_exit_intent_popup";
		$sgExitIntentSql = "DROP TABLE ". $sgExitIntentTable;

		$sgSubscriptionTable = $wpdb->prefix.$blogId."sg_subscription_popup";
		$sgSubscriptiontSql = "DROP TABLE ". $sgSubscriptionTable;

		$sgSubscribersTable = $wpdb->prefix.$blogId."sg_subscribers";
		$sgSubscribersSql = "DROP TABLE ". $sgSubscribersTable;

		$sgContactFormTable = $wpdb->prefix.$blogId."sg_contact_form_popup";
		$sgContactFormSql = "DROP TABLE ". $sgContactFormTable;

		$sgPopupInPages = $wpdb->prefix.$blogId."sg_popup_in_pages";
		$sgPopupInPagesSql = "DROP TABLE ". $sgPopupInPages;

		$sgSusbsErrorLog = $wpdb->prefix.$blogId."sg_subscription_error_log";
		$sgSusbsErrorLogSql = "DROP TABLE ". $sgSusbsErrorLog;

		$wpdb->query($popupIframeSql);
		$wpdb->query($popupVideoSql);
		$wpdb->query($popupAgeRestrictionSql);
		$wpdb->query($popupCountdownSql);
		$wpdb->query($popupSocialSql);
		$wpdb->query($sgExitIntentSql);
		$wpdb->query($sgSubscriptiontSql);
		$wpdb->query($sgSubscribersSql);
		$wpdb->query($sgContactFormSql);
		$wpdb->query($sgPopupInPagesSql);
		$wpdb->query($sgSusbsErrorLogSql);
		delete_option("SG_MULTIPLE_POPUP");
		delete_option("SG_ALL_POSTS");
		delete_option("SG_ALL_PAGES");
	}

	public static function uninstall()
	{
		$obj = new self();
		$obj->uninstallTables('');

		if (is_multisite()) {
			$stites = wp_get_sites();
			foreach($stites as $site) {
				$blogsId = $site['blog_id']."_";
				$obj->uninstallTables($blogsId);
			}
		}
		
		// Delete popup review option
		delete_option("SG_COLOSE_REVIEW_BLOCK");
	}
}
