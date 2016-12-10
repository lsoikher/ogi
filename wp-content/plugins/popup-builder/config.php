<?php
if (!defined( 'ABSPATH' )) {
	exit();
}

define("SG_APP_POPUP_PATH", dirname(__FILE__));
define('SG_APP_POPUP_URL', plugins_url('', __FILE__));
define('SG_APP_POPUP_ADMIN_URL', admin_url());
define('SG_APP_POPUP_FILE', plugin_basename(__FILE__));
define('SG_APP_POPUP_FILES', SG_APP_POPUP_PATH . '/files');
define('SG_APP_POPUP_CLASSES', SG_APP_POPUP_PATH . '/classes');
define('SG_APP_POPUP_JS', SG_APP_POPUP_PATH . '/javascript');
define('SG_APP_POPUP_HELPERS', SG_APP_POPUP_PATH . '/helpers/');
define('SG_APP_POPUP_TABLE_LIMIT', 15);
define('SG_POPUP_VERSION', 2.45);
define('SG_POPUP_PRO_URL', 'http://popup-builder.com/');
define('SG_POPUP_EXTENTIONS_URL', 'http://popup-builder.com/extensions');
define('SG_MAILCHIMP_EXTENTION_URL', 'http://popup-builder.com/mailchimp/');
define('SG_IP_TO_COUNTRY_SERVICE_TIMEOUT', 2);
define('SG_IP_TO_COUNTRY_SERVICE_URL', 'http://sygnoos.in/ip2data/?ip=');
define('SG_IP_TO_COUNTRY_SERVICE_TOKEN', 'd=b32e509a0c6da4147e7903f4bc0b60aa');
define("SG_SHOW_POPUP_REVIEW", get_option("SG_COLOSE_REVIEW_BLOCK"));
define("SG_POSTS_PER_PAGE", 1000);
/*Example 1 minute*/
define("SG_FILTER_REPEAT_INTERVAL", 1);
define("SG_POST_TYPE_PAGE", "allPages");
define("SG_POST_TYPE_POST", "allPosts");

define('POPUP_BUILDER_PKG_FREE', 1);
define('POPUP_BUILDER_PKG_SILVER', 2);
define('POPUP_BUILDER_PKG_GOLD', 3);
define('POPUP_BUILDER_PKG_PLATINUM', 4);
$POPUP_TITLES = array(
	'image' => 'Image',
	'html' => 'HTML',
	'fblike' => 'Facebook',
	'iframe' => 'Iframe',
	'video' => 'Video',
	'shortcode' => 'Shortcode',
	'ageRestriction' => 'Age Restriction',
	'countdown' => 'Countdown',
	'social' => 'Social',
	'exitIntent' => 'Exit Intent',
	'subscription' => 'Subscription',
	'contactForm' => 'Contact Form',
);

require_once(dirname(__FILE__).'/config-pkg.php');
