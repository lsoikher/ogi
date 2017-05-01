<?php

/*
Plugin Name: ReadyGraph
Plugin URI: http://www.readygraph.com/ 
Version: 1.0.5
Author: ReadyGraph team
Description: ReadyGraph is a simple friend invite tool that drives large number of traffic to your site
Author URI: http://www.readygraph.com/
*/

// plugin updater
include_once('updater.php');

if (is_admin()) {
    $config = array(
        'slug' => plugin_basename(__FILE__),
        'proper_folder_name' => 'readygraph',
	'source_folder_name' => '',
        'api_url' => 'https://api.github.com/repos/baddth/readygraph-wordpress',
        'raw_url' => 'https://raw.github.com/baddth/readygraph-wordpress/master',
        'github_url' => 'https://github.com/baddth/readygraph-wordpress',
        'zip_url' => 'https://github.com/baddth/readygraph-wordpress/zipball/master',
        'sslverify' => true,
        'requires' => '3.0',
        'tested' => '3.5',
        'readme' => 'README.md',
        'access_token' => ''
    );
    new WP_GitHub_Updater($config);
}

class ReadyGraphSocialPlugins {
    function ReadyGraphSocialPlugins() {
	add_action('admin_menu', array(&$this, 'rg_create_menu'));
	add_action('wp_head', array(&$this, 'rg_script_head'));
	add_filter('the_content', array(&$this, 'rg_content_filter'), 10000);
	add_filter('post_thumbnail_html', array(&$this, 'rg_post_thumbnail_filter'), 10000);
	#add_filter('post_gallery', array(&$this, 'rg_gallery_filter'), 10000);
    }
    
    function rg_script_head() {
	$app_id = get_option('rg_application_id');
	if ($app_id === false) {
	    return;
	}
?>
	<script type="text/javascript" src="//www.readygraph.com/scripts/readygraph.js"></script>
	<script type="text/javascript">
	    ReadyGraph.setup({applicationId: '<?php echo $app_id; ?>', overrideFacebookSDK: true});
	    console.log('<?php echo get_the_title(); ?>');
	</script>
<?php
    }
    
    function rg_post_thumbnail_filter($content) {
	$full_content = apply_filters('the_content', get_the_content());
	return $this->replace_content ($content, $this->extract_description($full_content));
    }
    
    function rg_content_filter($content) {
	return $this->replace_content ($content, $this->extract_description($content));
    }
    
    function rg_gallery_filter($output) {
	$description = $this->extract_description($output);
	if (trim($description) == '') $description = $this->extract_description(the_content());
	
	return $this->replace_content ($output, $description);
    }
    
    function extract_description($content) {
	$content = preg_replace ('/<script.*?\/script>/is', '', $content);
	$content = preg_replace ('/<style.*?\/style>/is', '', $content);
	$description = strip_tags($content);
	$description = substr(trim($description), 0, 250);
	
	return $description;
    }
    
    function replace_content($content, $description) {
	preg_match_all ( '/<img [^>]*>/ims',$content, $images );
	foreach ( $images[0] as $image) {
	    $class = preg_replace ('/.*class="([^"]*)/i', '\\1', $image);
	    if ($class == $image) $class = '';
	    
	    if (strpos($class, 'rgw-picture-og') !== false) continue;
	    
	    $src = preg_replace ('/.*src="([^"]*)/i', '\\1', $image);
	    if ($src == $image) $src = '';
	    
	    $rgw_data = 'rgw-data-title="'.htmlentities(get_the_title()).'" rgw-data-description="'.htmlentities($description).'" rgw-data-url="'.htmlentities(get_permalink()).'"';
	    
	    $replace = $image;
	    if ($src != '') {
		if ($class == '') {
		    $replace = preg_replace ('/class="([^"]*)/i', $rgw_data.' class="\\1 rgw-picture-og', $replace);
		}
		else {
		    $replace = preg_replace ('/src="([^"]*)/i', 'src="\\1" '.$rgw_data.' class="rgw-picture-og', $replace);
		}
	    }
	    
	    $content = str_replace ($image, $replace, $content);
	}
	return $content;
    }
    
    function rg_create_menu() {
	//create new top-level menu
	add_menu_page('ReadyGraph', 'ReadyGraph', 'administrator', __FILE__, array(&$this, 'rg_settings_page'), plugins_url('/images/rg_logo_sml.png', __FILE__));
	//call register settings function
	add_action( 'admin_init', array(&$this, 'rg_register_mysettings'));
    }
    
    function rg_register_mysettings() {
	//register our settings
	register_setting( 'rg-settings-group', 'rg_application_id' );
    }
    
    function rg_settings_page() {
?>
	<div class="wrap">
	    <h2>ReadyGraph Setting</h2>
		<form method="post" action="options.php">
		    <?php settings_fields( 'rg-settings-group' ); ?>
		    <table class="form-table">
			<tr valign="top">
			    <td scope="row" colspan="2">
				 To learn more about ReadyGraph&trade;, please visit <a href="http://www.readygraph.com" target="_blank">http://www.readygraph.com</a>
			    </td>
			</tr>
			<tr valign="top">
			    <th scope="row">ReadyGraph&trade; Application ID</th>
			    <td>
				 <input type="text" name="rg_application_id" value="<?php echo get_option('rg_application_id'); ?>" />
			    </td>
			</tr>
			<tr valign="top">
			    <td scope="row" colspan="2">
				<hr/>
			    </td>
			</tr>
		    </table>
		    <?php submit_button(); ?>
		</form>
	    </div>
	</div>
<?php
    }
}

add_action('init', create_function('', '$widget = new ReadyGraphSocialPlugins();'));
?>