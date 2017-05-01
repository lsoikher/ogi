<?php
/*
	Plugin Name: Floating Social Media Links
	Plugin URI: http://celloexpressions.com/plugins/floating-social-media-links
	Description: Add a clean and simple floating frame, with social media and/or custom links, to your website/blog.
	Version: 1.5.2
	Author: Nick Halsey
	Author URI: http://celloexpressions.com/
	License: GPL2
*/

/*  Copyright 2013 Nick Halsey (email : halseyns@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Set-up Action and Filter Hooks
if ( is_admin() ){
	register_activation_hook(__FILE__, 'fsml_add_defaults');
	register_uninstall_hook(__FILE__, 'fsml_delete_plugin_options');
	add_action('admin_init', 'fsml_init' );
	add_action('admin_menu', 'fsml_add_options_page');
	add_filter( 'plugin_action_links', 'fsml_plugin_action_links', 10, 2 );
}

// Delete options table entries ONLY when plugin deactivated AND deleted
function fsml_delete_plugin_options() {
	delete_option('fsml_options');
}

// Define default option settings
function fsml_add_defaults() {
	$tmp = get_option('fsml_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))){
		delete_option('fsml_options'); 
		$arr = array(	
						'plugin_version' => '1.5.2',
						
						//general options
						'displaymethod' => 'custom',
						'expandtitle' => 'Show Social Links',
						'hsanimation' => 'jqhs',
						'fixeddynamic' => 'fixed',
						'closeoption' => 'no',
						'ie7fix' => '',
						'hidefromsmall' => '1',
						'metaviewport' => '',
						'hidesize' => '800',
						'usecookies' => '0',
						'cookielength' => '1',
						'chk_default_options_db' => '',	
						'genadvanced' => 'hidden',
						'apadvanced' => 'hidden',
						'theme' => 'bright',
						'settingstab' => 'general',
						'sitesubmitted' => 'no',
						'outputcss' => '',

						//frame contents
						'facebookurl' => '',
						'facebookurltitle' => 'Visit Our Facebook Page',
						'enablefacebook' => '1',
						
						'youtubeurl' => '',
						'youtubeurltitle' => 'Visit Our YouTube Channel',
						'enableyoutube' => '1',
					
						'twitterurl' => '',
						'twitterurltitle' => 'View Our Twitter Feed',
						'enabletwitter' => '1',
					
						//buttons
						'likelocs' => 'bylinks',
						'fblike' => '',
						'tolike' => 'fb',
						'fblikeincroot' => 'no',
						'twfollow' => '',
						'ytsub' => '',
						'ytuseridnum' => '',
						'fbsend' => '',
							
						//custom options
						'numactivecustom' => '0',
						
						'customurl1' => '',
						'customurltitle1' => 'Visit Our Blog',
						'customurlimage1' => plugins_url('img/blog-default.png', __FILE__),
						'customurl2' => '',
						'customurltitle2' => 'More...',
						'customurlimage2' => plugins_url('img/more-default.png', __FILE__),
						'customurl3' => '',
						'customurltitle3' => 'More...',
						'customurlimage3' => plugins_url('img/more-default.png', __FILE__),
						'customurl4' => "",
						'customurltitle4' => 'More...',
						'customurlimage4' => plugins_url('img/more-default.png', __FILE__),
						'customurl5' => "",
						'customurltitle5' => 'More...',
						'customurlimage5' => plugins_url('img/more-default.png', __FILE__),
						'customurl6' => "",
						'customurltitle6' => 'More...',
						'customurlimage6' => plugins_url('img/more-default.png', __FILE__),
						'customurl7' => '',
						'customurltitle7' => 'More...',
						'customurlimage7' => plugins_url('img/more-default.png', __FILE__),
						
						'customtarget1' => '1',
						'customtarget2' => '1',
						'customtarget3' => '1',
						'customtarget4' => '1',
						'customtarget5' => '1',
						'customtarget6' => '1',
						'customtarget7' => '1',
						
						'enablecustom1' => '',
						'enablecustom2' => '',
						'enablecustom3' => '',
						'enablecustom4' => '',
						'enablecustom5' => '',
						'enablecustom6' => '',
						'enablecustom7' => '',
					
						//appearance
						'theme' => 'bright',
						'leftright' => 'right',
						'size' => 'snormal',
						'customwidth' => '',
						'customtop' => '5',
						'custombr' => '',
						'topunits' => 'pct',
						'customzindex' => '',
						'framehovereffect' => 'yes',
						'hovereffect' => 'yes',
						'borderradius' => 'none',
						'border' => 'no',
						'shadow' => 'no',
						'colorscheme' => 'light',
						'backgroundcolor' => '#ffffff',
						'bordercolor' => '#ababab',
						'shbw' => 'black',
						'frameshadow' => 'black',
						'fblikecolor' => 'light',
						'customcss' => ''
		);
		update_option('fsml_options', $arr);
	}
}

// Init plugin options to white list our options
function fsml_init(){
	register_setting( 'fsml_plugin_options', 'fsml_options', 'fsml_validate_options' );
}

// Add menu page
function fsml_add_options_page(){
	add_options_page('Floating Social Media Links Settings', 'Floating Social Media Links', 'manage_options', __FILE__, 'fsml_render_form');
}

// Prepare the media uploader
function fsml_admin_scripts(){
	if( function_exists('wp_enqueue_media') ) { wp_enqueue_media(); } //wrap in function exists to prevent php fatal error for people on wp pre-3.5
	wp_enqueue_script('fsml-admin', plugins_url('/fsml-admin.js', __FILE__), array('wp-color-picker', 'jquery'), false, false); // won't enqueue pre-3.5, so pretty much can't use 3.4 with plugin anymore
	wp_enqueue_style('fsml-admin', plugins_url('/fsml-admin.css', __FILE__));
	if(wp_style_is('wp-color-picker', 'registered')){
		wp_enqueue_style( 'wp-color-picker' );
	}
}
if (is_admin() && isset($_GET['page']) && $_GET['page'] == 'floating-social-media-links/floating-social-media-links.php') {
	add_action('admin_enqueue_scripts', 'fsml_admin_scripts');
}

//MUST be called after $options is defined, and $input must be passed
function fsml_perform_update( $version_from = '1.1.0', $input ) {
	// additions since most recent major version
	if(!array_key_exists('customcss',$input))
		$input['customcss'] = '';
	if(!array_key_exists('outputcss',$input))
		$input['outputcss'] = '';
		
	if(!array_key_exists('theme',$input))
		$input['theme'] = 'custom';
	
	if(!array_key_exists('framehovereffect',$input))
		$input['framehovereffect'] = 'no';
	
	elseif ($version_from == '1.4.0' || $version_from == '1.4.1' ) {
		//nothing added in 1.4.1, so group these together
		//1.4.1 additions
		if(!array_key_exists('hidefromsmall',$input)) {
			$input['hidefromsmall'] = '';
		}
		if(!array_key_exists('hidesize',$input)) {
			$input['hidesize'] = '600';
		}
		if(!array_key_exists('customwidth',$input)) {
			$input['customwidth'] = '';
		}
	}
	else {
		//1.4.1 additions
		if(!array_key_exists('hidefromsmall',$input)) {
			$input['hidefromsmall'] = '';
		}
		if(!array_key_exists('hidesize',$input)) {
			$input['hidesize'] = '600';
		}
		if(!array_key_exists('customwidth',$input)) {
			$input['customwidth'] = '';
		}
		if(!array_key_exists('sitesubmitted',$input)) {
			$input['sitesubmitted'] = 'no';
		}
		
		//pre-1.4.0 additions
		//add new options/defaults available since the version installed
		//define new/added features' variables if they are undefined
		if(!array_key_exists('cookielength',$input)) {
			$input['cookielength'] = '7';
		}
		if(!array_key_exists('genadvanced',$input)) {
			$input['genadvanced'] = 'hidden';
		}
		if(!array_key_exists('apadvanced',$input)) {
			$input['apadvanced'] = 'hidden';
		}
		//bring over values from deprecated inputs
		//isActive is now replaced by an option to output site wide through wp_footer or through custom template tags
		//probably should have considered isActive value... but too late now...
		if(!array_key_exists('displaymethod',$input)) {
			$input['displaymethod'] = 'auto';
		}
		if(!array_key_exists('hsanimation',$input)) {
			$input['hsanimation'] = 'jqhs';
		}
		if(!array_key_exists('expandtitle',$input)) {
			$input['expandtitle'] = 'Show Social Links';
		}
		if(!array_key_exists('fixeddynamic',$input)) {
			$input['fixeddynamic'] = 'fixed';
		}
		if(!array_key_exists('borderradius',$input)) {
			$input['borderradius'] = 'med';
		}
		if(!array_key_exists('shadow',$input)) {
			$input['shadow'] = 'yes';
		}
		if(!array_key_exists('topunits',$input)) {
			$input['topunits'] = 'px';
		}
		if(!array_key_exists('apadvanced',$input)) {
			$input['apadvanced'] = 'hidden';
		}
		
		//if one isn't there, none are (probably)
		if(!array_key_exists('customtarget7',$input)) {
			$input['customtarget1'] = '1';
			$input['customtarget2'] = '1';
			$input['customtarget3'] = '1';
			$input['customtarget4'] = '1';
			$input['customtarget5'] = '1';
			$input['customtarget6'] = '1';
			$input['customtarget7'] = '1';
		}
		
		//Remove options linking to legacy default images
		if($input["customurlimage2"] == plugins_url('img/more-default1.png', __FILE__))
			$input['customurlimage2'] = plugins_url('img/more-default.png', __FILE__);
		if($input["customurlimage3"] == plugins_url('img/more-default2.png', __FILE__))
			$input['customurlimage3'] = plugins_url('img/more-default.png', __FILE__);
		if($input["customurlimage4"] == plugins_url('img/more-default3.png', __FILE__))
			$input['customurlimage4'] = plugins_url('img/more-default.png', __FILE__);
		if($input["customurlimage5"] == plugins_url('img/more-default4.png', __FILE__))
			$input['customurlimage5'] = plugins_url('img/more-default.png', __FILE__);
		if($input["customurlimage6"] == plugins_url('img/more-default5.png', __FILE__))
			$input['customurlimage6'] = plugins_url('img/more-default.png', __FILE__);
		if($input["customurlimage7"] == plugins_url('img/more-default7.png', __FILE__))
			$input['customurlimage7'] = plugins_url('img/more-default.png', __FILE__);
	}
	
	// update the plugin_version constant
	$input['plugin_version'] = '1.5.2';
	
	
	
	$options = $input;
	
	return $options;
}

// Render the Plugin options form
function fsml_render_form(){
	//for dynamic admin js
	$tab = fsml_897_getOption('settingstab');
	$theme = fsml_897_getOption('theme');
	if(!$theme)
		$theme = 'custom';
	$apadvanced = fsml_897_getOption('apadvanced');
	$gadvanced = fsml_897_getOption('genadvanced');
	$nac = fsml_897_getOption('numactivecustom');
?>
	<div class="wrap">
		<script>
		function loadTabs(){
			switchTabs('<?php echo $tab; ?>');
		}	
		jQuery(document).ready(function(){
			//set the initial page to the most recent one
			<?php if($apadvanced == 'hidden') { echo 'jQuery(".apadvanced").hide();' . 'jQuery("#hideapadvanced").hide();' ; } ?>
			<?php if($apadvanced == 'shown') { echo 'jQuery("#showapadvanced").hide();' ; } ?>
			<?php if($theme != 'custom') { echo 'jQuery(".dadvanced").hide();' ; } ?>
			<?php if($gadvanced == 'hidden') { echo 'jQuery(".genadvanced").hide();' . 'jQuery("#hidegenadvanced").hide();' ; } ?>
			<?php if($gadvanced == 'shown') { echo 'jQuery("#showgenadvanced").hide();' ; } ?>
				<?php
			// the jquery for showing more custom link options in the form, using php to write the jquery to show the correct number of links initially
			//start by hiding all of the add another custom link links except for the one after the last active option
			  $customlinksit = 0;
				while($customlinksit < $nac) {
					echo ('jQuery("#addcustom' . $customlinksit . '").hide();
					');
					$customlinksit++;
				}
				$customlinksit = 7;
				while($customlinksit > $nac) {
					echo ('jQuery("#addcustom' . $customlinksit . '").hide();
					');
					$customlinksit--;
				}
			// show the next custom link options on clicking the add another link buttons
				$customlinksit = 0;
				while($customlinksit < 7) {
					echo ( "jQuery('#addcustom". $customlinksit . "').click(function(){ jQuery(this).next('tr').show(500, function(){ jQuery(this).prev().hide(300); jQuery(this).next('tr').show(300);}); });
					" );
					$customlinksit++; 
				}?>
			});
		</script>
		
		<!-- Display Plugin Header, then the subpage navigation tabs -->
		<h2>Floating Social Media Links Settings</h2><?php // wp messages hook into the page content's first <h2>, so I can't use h1 here, so I do a modified h1 for the nav... ?>
		<?php if(!wp_style_is('wp-color-picker', 'registered')){ ?><h4 style="color: #f00;">WARNING: you are using an out-of-date version of WordPress that this plugin no longer supports. You will not be able to use the color pickers or upload media directly, but <em>if there is also a JavaScript error on this page, you will only be able to do very minimal things with this plugin until you upgrade to WordPress 3.5 or higher</em>. If you have recently updated this plugin, everything should still work on the front end of your site, but your administration capabilities will be quite limited.</h4> <?php } ?>
		<h4 id="jstest">ERROR: JavaScript has failed to load this page properly! If there is a JavaScript error, it's likely that some other plugin is interfering or JavaScript may be turned off. JavaScript is required to access most of the options on this page.</h4>
		<ul id="fsmlsubpages">
			<li id="fsmlsuboptionspage_general" class="fsmlnav" onclick="switchTabs('general')"><h1>General</h1></li>
			<li id="fsmlsuboptionspage_content" class="fsmlnav" onclick="switchTabs('content')"><h1>Links/Icons</h1></li>
			<li id="fsmlsuboptionspage_buttons" class="fsmlnav" onclick="switchTabs('buttons')"><h1>Buttons</h1></li>
			<li id="fsmlsuboptionspage_design" class="fsmlnav" onclick="switchTabs('design')"><h1>Design</h1></li>
			<li id="fsmlsuboptionspage_appearance" class="fsmlnav" onclick="switchTabs('appearance')"><h1>Form</h1></li>
		</ul>
		
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php" id="fsmlopform">
			<?php settings_fields('fsml_plugin_options'); ?>
			<?php $options = get_option('fsml_options'); 
			//check if this is the most recent version of the plugin
			if(!array_key_exists('plugin_version',$options)||$options['plugin_version'] != '1.5.2') {
				if(array_key_exists('plugin_version',$options))
					$ver = $options['plugin_version'];
				else
					$ver = '1.1.0';
				$options = fsml_perform_update( $ver, $options );
			}
			?>
			<!-- current tab option so the plugin remembers the latest tab after saving/refreshing/returning -->
			<input type="hidden" name="fsml_options[settingstab]" id="settingstab" value="<?php echo $options['settingstab']; ?>" />
			
			<!-- need this here to save it... so actually it isn't being recorded by any versions prior to 1.5.0, and this was always running on each page load... -->
			<input type="hidden" name="fsml_options[plugin_version]" value="<?php echo $options['plugin_version']; ?>" />
			
			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			
			<!-- the general options page -->
			<table class="form-table" id="fsml_general">
				<!-- Activate the output? -->
				<tr>
					<th scope="row"><h3><b>When should the frame be shown?</b></h3></th>
					<td>
						<label><input name="fsml_options[displaymethod]" type="radio" value="auto" <?php checked('auto', $options['displaymethod']); ?> /><b> On Every Page </b><span style="color:#666666;margin-left:9px;">(links will be shown on every page &mdash; make sure you set the links before choosing this option)</span></label><br />
						<label><input name="fsml_options[displaymethod]" type="radio" value="custom" <?php checked('custom', $options['displaymethod']); ?> /><b> On Customized Pages </b><span style="color:#666666;margin-left:9px;">(links will <i>NOT</i> be shown until you put the code in your theme's template files)</span></label>
						<p>Code to put in templates: <code>&lt;?php floating_social_media_links() ?&gt;</code>. More information in the <a href="http://wordpress.org/extend/plugins/floating-social-media-links/faq/" target="_blank">FAQ</a>.</p>
						<label><input name="fsml_options[displaymethod]" type="radio" value="widgetbeta" <?php checked('widgetbeta', $options['displaymethod']); ?> /> Using the TEMPORARY FSML Contents with Widgets Plugin <span style="color:#666666;margin-left:9px;"><a href="http://celloexpressions.com/dev/floating-social-media-links/fsml-contents-widgets-temporary-plugin-information/" target="_blank"><i>learn more...</i></a></span></label>
					</td>
				</tr>
				<!-- Allow Viewers to Hide? -->
				<tr style="border-top:#ddd 1px solid;">
					<th scope="row">Allow site visitors to hide the frame?</th>
					<td>
						<label id="shyes"><input name="fsml_options[closeoption]" type="radio" value="yes" <?php checked('yes', $options['closeoption']); ?> /> Yes, and visitors CAN re-open <span style="color:#666666;margin-left:9px;"> (a small x in the top corner of the frame hides it, and a small icon continues to float which will reopen the whole frame)</span></label><br />
						<label id="noshow"><input name="fsml_options[closeoption]" type="radio" value="yesnoopen" <?php checked('yesnoopen', $options['closeoption']); ?> /> Yes, visitors CAN'T re-open <span style="color:#666666;margin-left:9px;">(user can still hide but not reopen without refreshing the page)</span></label><br />
						<label id="sthidden"><input name="fsml_options[closeoption]" type="radio" value="starthidden" <?php checked('starthidden', $options['closeoption']); ?> /> Yes, but start with the frame hidden <span style="color:#666666;margin-left:9px;">(user clicks icon to show the frame, then can hide it again)</span></label><br />
						<label id="nohide"><input name="fsml_options[closeoption]" type="radio" value="no" <?php checked('no', $options['closeoption']); ?> /> No <span style="color:#666666;margin-left:9px;">(the frame may block content for visitors with small window sizes)</span></label>
					</td>
				</tr>
				<!-- Hideshow Animation -->
				<tr id="hsanimation" <?php if($options['closeoption'] == 'no'){ echo 'style="display: none"'; }?>>
					<th scope="row">Animation Showing/Hiding Frame</th>
					<td>
						<label><input name="fsml_options[hsanimation]" type="radio" value="jqhs" <?php checked('jqhs', $options['hsanimation']); ?> /> Zoom and Fade </label><br />
						<label><input name="fsml_options[hsanimation]" type="radio" value="fade" <?php checked('fade', $options['hsanimation']); ?> /> Fade In/Out <span style="color:#666666;margin-left:9px;">doesn't work well if using frame fade style</span></label><br />
						<label><input name="fsml_options[hsanimation]" type="radio" value="none" <?php checked('none', $options['hsanimation']); ?> /> None <span style="color:#666666;margin-left:9px;">(hide/show will be instant)</span></label>
					</td>
				</tr>
				<!-- Expand Title -->
				<tr id="expandtitle" <?php if($options['closeoption'] == 'no' || $options['closeoption'] == 'yesnoopen'){ echo 'style="display: none"'; }?>>
					<th scope="row">"Show Frame" Icon Title</th>
					<td><input type="text" size="15" name="fsml_options[expandtitle]" value="<?php echo $options['expandtitle']; ?>" /></td>
				</tr>
				<!--NOTICE-->
				<tr style="border-top:#ddd 1px solid;"><td colspan="2">In order for this plugin to function properly, your theme must include the <code>&lt;?php wp_head(); ?&gt;</code> and <code>&lt;?php wp_footer(); ?&gt;</code> action hooks. If the floating frame is not present or displays incorrectly, please check for these in your theme template files. <a href="http://codex.wordpress.org/Function_Reference/wp_head" target="_blank">Read WordPress' documentation on the wp_head()</a> <a href="http://codex.wordpress.org/Function_Reference/wp_footer" target="_blank">and wp_footer() action hooks here.</a></td></tr>
				<!-- advanced general options -->
				<tr><td colspan="2"><p id="showgenadvanced"><b><a>Show Advanced Options...</a></b></p></td></tr>
				<tr><td colspan="2"><p id="hidegenadvanced"><b><a>Hide Advanced Options</a></b></p></td></tr>
				<tr style="display:none;"><td colspan="2"><input type="hidden" name="fsml_options[genadvanced]" id="genadvanced" value="<?php echo $options['genadvanced']; ?>"/></td></tr>
				<!-- IE7 Fix -->
				<tr style="border-top:#ddd 1px solid;" class="genadvanced">
					<th scope="row">IE7- Compatibility</th>
					<td>
						<label><input name="fsml_options[ie7fix]" type="checkbox" value="1" <?php if (isset($options['ie7fix'])) { checked('1', $options['ie7fix']); } ?> /> Check this if your site needs to be compatibile with Internet Explorer 7 and below.</label>
						<br />Please note that effects such as shadows, opacity, and rounded corners are not available in IE8 or below. I strongly recommend requiring or at least recommending that your site's visitors use up-to-date browsers, try the <a href="http://wordpress.org/extend/plugins/browser-rejector/" target="_blank">Browser Rejector Wordpress Plugin</a> to do this easily.
					</td>
				</tr>
				<!-- Hide for Small Windows/Devices -->
				<tr class="genadvanced">
					<th scope="row">Minimum Window/Screen Width</th>
					<td>
						<label ><input name="fsml_options[hidefromsmall]" id="hidesmall" type="checkbox" value="1" <?php if (isset($options['hidefromsmall'])) { checked('1', $options['hidefromsmall']); } ?> /> Hide the frame when window/device width <em>*renders*</em> as </label>
						<input type="text" size="3" name="fsml_options[hidesize]" value="<?php echo $options['hidesize']; ?>" />px or less
					</td>
				</tr>
				<tr class="genadvanced" id="metaviewport" <?php if(!fsml_897_getOption('hidefromsmall')){ echo 'style="display:none;"'; } ?>>
					<th scope="row">Enforce Minimum Width with Meta Viewport Tag</th>
					<td>
						<label><input name="fsml_options[metaviewport]" type="checkbox" value="1" <?php if (isset($options['metaviewport'])) { checked('1', $options['metaviewport']); } ?> /> Force devices to render appropriately given their DPI (for example, a high-resolution smartphone might be 1200 pixels tall, but a laptop may also be 1200 pixels wide; with this option, the phone will pretend it's only 300 pixels wide so that the pixels are approximately the same size accross devices, and therefore the phone won't display the floating links even if it's high resoloution). Pease note: this option changes how mobile devices display your website. Please check how your site displays on such devices before keeping this option. See <a href="http://celloexpressions.com/dev/articles/scaling-websites-accross-modern-devices/" target="_blank">my article on how device breakpoints work</a> for more information. Some "responsive" themes will already have this code, so you won't see a difference.</label>
					</td>
				</tr>
				<!-- Enable Cookies -->
				<tr class="genadvanced">
					<th scope="row">Remember if Visitor Hides Frame?</th>
					<td>
						<label><input name="fsml_options[usecookies]" id="usecookies" type="checkbox" value="1" <?php if (isset($options['usecookies'])) { checked('1', $options['usecookies']); } ?> /> Remember preferrences, using cookies</label>
					</td>
				</tr>
				<!-- Cookie Duration -->
				<tr class="genadvanced" id="cookielength" <?php if(!fsml_897_getOption('usecookies')){ echo 'style="display:none;"'; } ?>>
					<th scope="row">Cookie Length</th>
					<td>
						Remember for <input type="text" size="3" name="fsml_options[cookielength]" value="<?php echo $options['cookielength']; ?>" /> days (decimals allowed, ie ".5")
					<p><b>Cookie Information (for terms, etc.):</b> a cookie named "fsmlOpen" is stored (for [X] days) in order to remember your preferences for displaying or hiding our social media links floating frame.</p>
					</td>
				</tr>
				<!-- reset settings? -->
				<tr style="border-top:#dddddd 1px solid; display:none;">
					<th scope="row">Advanced Database Options (primarily for debugging)</th>
					<td>
						<label><input name="fsml_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon plugin reactivation</span>
					</td>
				</tr>
			</table>
			
			<?php
				//prepare variables
				$fsmlimgbase = plugins_url('/img/', __FILE__);
				$customimgdesc = '<span style="color:#666666;">Enter the URL of or upload a new image for the custom url icon. Images will be resized horizontally to fit the frame size, but their aspect ratio will remain intact (use wordpress generated thumbnails - 150px by 150px - for square images). A border radius of 8px (rounded corners) and an opacity (semi-transparency) filter will be applied (unless you disable it in the "appearance" settings tab).</span>';
			?>
			<table class="form-table" id="fsml_content">
			<!-- the content subpage -->
			<tr><td colspan="2"><p><small>If you need social media <em>sharing</em>, try the <a href="http://wordpress.org/plugins/quickshare">QuickShare</a> plugin. Floating Social Media Links has a slightly different purpose. Both plugins can, of course, be used in tandem.</small></p></td></tr>
				<!-- Facebook Page URL -->
				<tr>
					<th scope="row">Facebook Page URL<br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablefacebook]" type="checkbox" value="1" <?php if (isset($options['enablefacebook'])) { checked('1', $options['enablefacebook']); } ?> /><span style="color: #666666;"> Show Link</span></label>
					</th>
					<td>
						http://facebook.com/<input type="text" size="57" name="fsml_options[facebookurl]" value="<?php echo $options['facebookurl']; ?>" /><br />
						<span style="color:#666666;">Enter the URL of your facebook page.</span><br />
						Link Title:    <input type="text" size="50" name="fsml_options[facebookurltitle]" value="<?php echo $options['facebookurltitle']; ?>" /><br /><br />
					</td>
					<td>
						<b>Link/Image Previews</b><br />
						<a href="http://facebook.com/<?php echo $options['facebookurl']; ?>" target="_blank" title="<?php echo $options['facebookurltitle']; ?>" >
						<img src="<?php echo $fsmlimgbase . 'facebook.png'; ?>" alt="facebook" class="fsml_testimg" /></a>
					</td>
				</tr>
				
				<!-- Youtube Channel URL -->
				<tr>
					<th scope="row">Youtube Channel URL<br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enableyoutube]" type="checkbox" value="1" <?php if (isset($options['enableyoutube'])) { checked('1', $options['enableyoutube']); } ?> /><span style="color: #666666;"> Show Link</span></label>
					</th>
					<td>
						http://youtube.com/<input type="text" size="57" name="fsml_options[youtubeurl]" value="<?php echo $options['youtubeurl']; ?>" /><br />
						<span style="color:#666666;">Enter the URL of your youtube channel.</span><br /><br />
						Link Title:    <input type="text" size="50" name="fsml_options[youtubeurltitle]" value="<?php echo $options['youtubeurltitle']; ?>" /><br /><br />
					</td>
					<td>
						<a href="http://youtube.com/<?php echo $options['youtubeurl']; ?>" target="_blank" title="<?php echo $options['youtubeurltitle']; ?>" >
						<img src="<?php  echo $fsmlimgbase . 'youtube.png'; ?>" alt="youtube" class="fsml_testimg" /></a>
					</td>
				</tr>
				
				<!-- Twitter Feed URL -->
				<tr>
					<th scope="row">Twitter Feed URL<br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enabletwitter]" type="checkbox" value="1" <?php if (isset($options['enabletwitter'])) { checked('1', $options['enabletwitter']); } ?> /><span style="color: #666666;"> Show Link</span></label>
					</th>
					<td>
						http://twitter.com/<input type="text" size="57" name="fsml_options[twitterurl]" value="<?php echo $options['twitterurl']; ?>" /><br />
						<span style="color:#666666;">Enter the URL of your twitter page.</span><br /><br />
						Link Title:    <input type="text" size="50" name="fsml_options[twitterurltitle]" value="<?php echo $options['twitterurltitle']; ?>" /><br /><br />
					</td>
					<td>
						<a href="http://twitter.com/<?php echo $options['twitterurl']; ?>" target="_blank" title="<?php echo $options['twitterurltitle']; ?>" >
						<img src="<?php echo $fsmlimgbase . 'twitter.png'; ?>" alt="twitter" class="fsml_testimg" /></a>
					</td>
				</tr>
				
				<tr id="addcustom0"><td colspan="2"><div class="fsmlac">Add A Custom Link</div></td></tr>
				<!-- Custom URL 1 (Blog, etc.)-->
				<tr id="fsmlcustomlink1" <?php if($options['numactivecustom'] < 1) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 1 (Blog, etc.)<br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom1]" type="checkbox" value="1" <?php if (isset($options['enablecustom1'])) { checked('1', $options['enablecustom1']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl1]" value="<?php echo $options['customurl1']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle1]" value="<?php echo $options['customurltitle1']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget1]" type="checkbox" value="1" <?php if (isset($options['customtarget1'])) { checked('1', $options['customtarget1']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage1" name="fsml_options[customurlimage1]" value="<?php echo $options['customurlimage1']; ?>" />
						<input class="img_upload_button" type="button" class="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					<td>
						<a href="<?php echo $options['customurl1']; ?>" target="_blank" title="<?php echo $options['customurltitle1']; ?>">
						<img src="<?php echo $options['customurlimage1']; ?>" alt="custom image" class="fsml_testimg" id="customurlimage1_preview"/></a>
					</td>
				</tr>
				<tr id="addcustom1"><td colspan="2"><div class="fsmlac">Add Another Custom Link</div></td></tr>
				<!-- Custom URL 2 -->
				<tr id="fsmlcustomlink2" <?php if($options['numactivecustom'] < 2) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 2 <br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom2]" type="checkbox" value="1" <?php if (isset($options['enablecustom2'])) { checked('1', $options['enablecustom2']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl2]" value="<?php echo $options['customurl2']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle2]" value="<?php echo $options['customurltitle2']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget2]" type="checkbox" value="1" <?php if (isset($options['customtarget2'])) { checked('1', $options['customtarget2']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage2" name="fsml_options[customurlimage2]" value="<?php echo $options['customurlimage2']; ?>" />
						<input class="img_upload_button" type="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					
					<td>
						<a href="<?php echo $options['customurl2']; ?>" target="_blank" title="<?php echo $options['customurltitle2']; ?>">
						<img src="<?php echo $options['customurlimage2']; ?>" class="fsml_testimg" alt="cutom image" id="customurlimage2_preview"/></a>
					</td>
				</tr>
				<tr id="addcustom2"><td colspan="2"><div class="fsmlac">Add Another Custom Link</div></td></tr>
				<!-- Custom URL 3 -->
				<tr id="fsmlcustomlink3" <?php if($options['numactivecustom'] < 3) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 3 <br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom3]" type="checkbox" value="1" <?php if (isset($options['enablecustom3'])) { checked('1', $options['enablecustom3']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl3]" value="<?php echo $options['customurl3']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle3]" value="<?php echo $options['customurltitle3']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget3]" type="checkbox" value="1" <?php if (isset($options['customtarget3'])) { checked('1', $options['customtarget3']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage3" name="fsml_options[customurlimage3]" value="<?php echo $options['customurlimage3']; ?>" />
						<input class="img_upload_button" type="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					
					<td>
						<a href="<?php echo $options['customurl3']; ?>" target="_blank" title="<?php echo $options['customurltitle3']; ?>">
						<img src="<?php echo $options['customurlimage3']; ?>" class="fsml_testimg"  alt="cutom image" id="customurlimage3_preview"/></a>
					</td>
				</tr>
				<tr id="addcustom3"><td colspan="2"><div class="fsmlac">Add Another Custom Link</div></td></tr>
				<!-- Custom URL 4 -->
				<tr id="fsmlcustomlink4" <?php if($options['numactivecustom'] < 4) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 4 <br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom4]" type="checkbox" value="1" <?php if (isset($options['enablecustom4'])) { checked('1', $options['enablecustom4']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl4]" value="<?php echo $options['customurl4']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle4]" value="<?php echo $options['customurltitle4']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget4]" type="checkbox" value="1" <?php if (isset($options['customtarget4'])) { checked('1', $options['customtarget4']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage4" name="fsml_options[customurlimage4]" value="<?php echo $options['customurlimage4']; ?>" />
						<input class="img_upload_button" type="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					
					<td>
						<a href="<?php echo $options['customurl4']; ?>" target="_blank" title="<?php echo $options['customurltitle4']; ?>">
						<img src="<?php echo $options['customurlimage4']; ?>" class="fsml_testimg"  alt="cutom image" id="customurlimage4_preview"/></a>
					</td>
				</tr>
				<tr id="addcustom4"><td colspan="2"><div class="fsmlac">Add Another Custom Link</div></td></tr>
				<!-- Custom URL 5 -->
				<tr id="fsmlcustomlink5" <?php if($options['numactivecustom'] < 5) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 5 <br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom5]" type="checkbox" value="1" <?php if (isset($options['enablecustom5'])) { checked('1', $options['enablecustom5']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl5]" value="<?php echo $options['customurl5']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle5]" value="<?php echo $options['customurltitle5']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget5]" type="checkbox" value="1" <?php if (isset($options['customtarget5'])) { checked('1', $options['customtarget5']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage5" name="fsml_options[customurlimage5]" value="<?php echo $options['customurlimage5']; ?>" />
						<input class="img_upload_button" type="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					
					<td>
						<a href="<?php echo $options['customurl5']; ?>" target="_blank" title="<?php echo $options['customurltitle5']; ?>">
						<img src="<?php echo $options['customurlimage5']; ?>" class="fsml_testimg"  alt="cutom image" id="customurlimage5_preview"/></a>
					</td>
				</tr>
				<tr id="addcustom5"><td colspan="2"><div class="fsmlac">Add Another Custom Link</div></td></tr>
				<!-- Custom URL 6 -->
				<tr id="fsmlcustomlink6" <?php if($options['numactivecustom'] < 6) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 6 <br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom6]" type="checkbox" value="1" <?php if (isset($options['enablecustom6'])) { checked('1', $options['enablecustom6']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl6]" value="<?php echo $options['customurl6']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle6]" value="<?php echo $options['customurltitle6']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget6]" type="checkbox" value="1" <?php if (isset($options['customtarget6'])) { checked('1', $options['customtarget6']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage6" name="fsml_options[customurlimage6]" value="<?php echo $options['customurlimage6']; ?>" />
						<input class="img_upload_button" type="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					
					<td>
						<a href="<?php echo $options['customurl6']; ?>" target="_blank" title="<?php echo $options['customurltitle6']; ?>">
						<img src="<?php echo $options['customurlimage6']; ?>" class="fsml_testimg"  alt="cutom image" id="customurlimage6_preview"/></a>
					</td>
				</tr>
				<tr id="addcustom6"><td colspan="2"><div class="fsmlac">Add Another Custom Link</div></td></tr>
				<!-- Custom URL 7 -->
				<tr id="fsmlcustomlink7" <?php if($options['numactivecustom'] < 7) { echo 'style="display:none;"'; } ?>>
					<th scope="row">Custom URL 7 <br /><br />
						<label style="margin-left: 15px;" ><input name="fsml_options[enablecustom7]" type="checkbox" value="1" <?php if (isset($options['enablecustom7'])) { checked('1', $options['enablecustom7']); } ?> /><span style="color: #666666;"> Show Link</span></label>
						<p style="margin: 60px 0 0 40%;">Link Icon:</p>
					</th>
					<td>
						<input type="text" size="70" name="fsml_options[customurl7]" value="<?php echo $options['customurl7']; ?>" /><br />
						<span style="color:#666666;">Enter your Custom URL (ie yourdomain.com/blog/).</span><br /><br />
						Link Title:     <input type="text" size="30" name="fsml_options[customurltitle7]" value="<?php echo $options['customurltitle7']; ?>" /><label style="margin-left: 20px"><input name="fsml_options[customtarget7]" type="checkbox" value="1" <?php if (isset($options['customtarget7'])) { checked('1', $options['customtarget7']); } ?> /><span style="color: #666666;"> Open in New Tab</span></label><br /><br />
						<input type="text" size="100" id="customurlimage7" name="fsml_options[customurlimage7]" value="<?php echo $options['customurlimage7']; ?>" />
						<input class="img_upload_button" type="button" value="Upload Image" /><br />
						<?php echo $customimgdesc; ?>
					</td>
					
					<td>
						<a href="<?php echo $options['customurl7']; ?>" target="_blank" title="<?php echo $options['customurltitle7']; ?>">
						<img src="<?php echo $options['customurlimage7']; ?>" class="fsml_testimg"  alt="cutom image" id="customurlimage7_preview"/></a>
					</td>
				</tr>
			</table>
			
			
			<!--page for like, etc. button options-->
			<table class="form-table" id="fsml_buttons">
				<tr><td colspan="2"><p>These buttons are designed for "following" actions. If you're looking for "sharing" options, check out my <a href="http://wordpress.org/plugins/quickshare" target="_blank">QuickShare plugin</a>. Content-sharing should generally be attached to content (like posts, pages, media), not site-wide. Entity-sharing is better done through "following" actions initially.</p></td></tr>
				<!-- Activate Like, etc. Buttons? -->
				<tr>
					<th scope="row">Include:</th>
					<td>
						<label><input name="fsml_options[fblike]" id="actionfblike" type="checkbox" value="1" <?php if (isset($options['fblike'])) { checked('1', $options['fblike']); } ?> /> Facebook Like/Share Button</label><br />
							<!-- facebook send is no longer available. Like = share now though... -->
						<label><input name="fsml_options[twfollow]" id="actiontwfollow" type="checkbox" value="1" <?php if (isset($options['twfollow'])) { checked('1', $options['twfollow']); } ?> /> Twitter Follow Button</label><br />
						<label><input name="fsml_options[ytsub]" id="actionytsub" type="checkbox" value="1" <?php if (isset($options['ytsub'])) { checked('1', $options['ytsub']); } ?> /> YouTube Subscribe Button</label><br />
						<div id="ytuseridetc" <?php if(!fsml_897_getOption('ytsub')) { echo 'style="display: none;"'; } ?>>Youtube User Id <i>(required)</i>: <input type="text" size="22" name="fsml_options[ytuseridnum]" value="<?php echo $options['ytuseridnum']; ?>" /><br />
							<span style="color: #666666">Because youtube account names can change, youtube assigns every account a unique id number. 
							This number can be located by visiting your advanced account settings page at <a href="http://www.youtube.com/account_advanced" target="_blank">
							youtube.com/account_advanced</a>, logging in/ensuring you're logged into the correct account, and locating the 
							"YouTube User Id" which should look like a random string of letters and numbers, such as "VdEcgb35BalmfR8flzkxTA".</span>
						</div>
					</td>
				</tr>
				<tr id="fburltolike" <?php if(!fsml_897_getOption('fblike')) { echo 'style="display: none;"'; } ?>>
					<th scope="row">URL to Like (Facebook)</th>
					<td>
						<label><input name="fsml_options[tolike]" type="radio" value="fb" <?php checked('fb', $options['tolike']); ?> /> Facebook Page (default) <span style="color: #666;">(references the facebook (icon) link set in "links/icons" tab)</span></label><br />
						<label><input name="fsml_options[tolike]" type="radio" value="site" <?php checked('site', $options['tolike']); ?> /> Site Homepage <span style="color: #666;">(the homepage of your wordpress website/blog)</span></label>
					</td>
				</tr>
				<!-- Resize for Facebook is no longer necessary because of the plugin's custom implementation -->
				<!-- Facebook SDK is no longer necessary because of the plugin's custom implementation. No more load time pain! -->
				<tr>
					<th scope="row">Show buttons:</th>
					<td>
						<label><input name="fsml_options[likelocs]" type="radio" value="bylinks" <?php checked('bylinks', $options['likelocs']); ?> /> After Each Corresponding Link </label><br />
						<label><input name="fsml_options[likelocs]" type="radio" value="afterall" <?php checked('afterall', $options['likelocs']); ?> /> After All of the Links </label>
					</td>
				</tr>
			</table>
			
			
			<!-- the form page -->
			<table class="form-table" id="fsml_appearance">
				<!-- On the Left or Right? -->
				<tr >
					<th scope="row">Frame Location</th>
					<td>
						<label><input name="fsml_options[leftright]" type="radio" value="right" <?php checked('right', $options['leftright']); ?> /> Right <span style="color:#666666;margin-left:9px;">of the page</span></label><br />
						<label><input name="fsml_options[leftright]" type="radio" value="left" <?php checked('left', $options['leftright']); ?> /> Left <span style="color:#666666;margin-left:9px;">of the page</span></label>
					</td>
				</tr>
				<tr id="dynamicsize" <?php if($options['fixeddynamic'] != 'dynamic') { echo 'style="display: none;"'; } ?>>
					<th scope="row">Choose Size (dynamic % width):<br /><br /><span style="color: #666666; margin-left: 10px">Note the % width of your site.</span></th>
					<td>
						<label><input name="fsml_options[size]" type="radio" value="dsmall" <?php checked('dsmall', $options['size']); ?> /> Small - 4% <span style="color:#666666;margin-left:9px;"><i>Ideal for sites with widths of 92% or less.</i></span></label><br />
						<label><input name="fsml_options[size]" type="radio" value="dnormal" <?php checked('dnormal', $options['size']); ?> /> Medium - 5% <span style="color:#666666;margin-left:9px;"><i>Ideal for sites with widths of 90% or less.</i></span></label><br />
						<label><input name="fsml_options[size]" type="radio" value="dlarge" <?php checked('dlarge', $options['size']); ?> /> Large - 7%</label>
					</td>
				</tr>
				<!-- Size - only one set of options will be shown, based on fixed/dynamic width -->
				<tr id="fixedsize" <?php if($options['fixeddynamic'] == 'dynamic') { echo 'style="display: none;"'; } ?>>
					<th scope="row">Choose Size (static width):<br /><br /><span style="color: #666666; margin-left: 10px">WARNING: not all sizes will display properly in all screen/window sizes</span></th>
					<td>
						<label><input name="fsml_options[size]" type="radio" value="fsmall" <?php checked('fsmall', $options['size']); ?> /> Small</label><br />
						<label><input name="fsml_options[size]" type="radio" value="fnormal" <?php checked('fnormal', $options['size']); ?> /> Medium</label><br />
						<label><input name="fsml_options[size]" type="radio" value="flarge" <?php checked('flarge', $options['size']); ?> /> Large</label>
					</td>
				</tr>
				
				<!-- advanced appearance options -->
				<tr><td colspan="2"><h3 id="showapadvanced"><a>Show More Format Options...</a></h3></td></tr>
				<tr><td colspan="2"><h3 id="hideapadvanced"><a>Hide Extended Format Options</a></h3></td></tr>
				<tr style="display:none;"><td colspan="2"><input type="hidden" name="fsml_options[apadvanced]" id="apadvanced" value="<?php echo $options['apadvanced']; ?>"/></td></tr>
				<!-- Vertical Positioning -->
				<tr style="border-top: 1px solid #999;" class="apadvanced">
					<th scope="row">Set Top Margin:<br /><span style="color: #666666; margin-left: 10px">(Distance from top of window to top of frame. Default 5% used if not set.)</span></th>
					<td>
						<input type="text" size="3" name="fsml_options[customtop]" value="<?php echo $options['customtop']; ?>" />
						<select name='fsml_options[topunits]'>
							<option value='pct' <?php selected('pct', $options['topunits']); ?>>%</option>
							<option value='px' <?php selected('px', $options['topunits']); ?>>px</option>
							<option value='in' <?php selected('in', $options['topunits']); ?>>in</option>
							<option value='cm' <?php selected('cm', $options['topunits']); ?>>cm</option>
						</select>
					</td>
				</tr>
				<!-- Fixed or Dynamic Width? -->
				<tr class="apadvanced">
					<th scope="row">Static or Dynamic Width?<br /></th>
					<td>
						<label id="dynamicwidth"><input name="fsml_options[fixeddynamic]" type="radio" value="dynamic" <?php checked('dynamic', $options['fixeddynamic']); ?> /> Dynamic Width <span style="color:#666666;margin-left:9px;">(the frame is narrower on smaller windows/screens, wider on larger windows/screens)</span></label><br/>
						<label id="fixedwidth"><input name="fsml_options[fixeddynamic]" type="radio" value="fixed" <?php checked('fixed', $options['fixeddynamic']); ?> /> Static Width <span style="color:#666666;margin-left:9px;">(the width of the frame doesn't change, consistent display size)</span></label><br />
					</td>
				</tr>
				<!-- Custom Width -->
				<tr class="apadvanced">
					<th scope="row">Override size with custom width</th>
					<td>
						<input type="text" size="3" name="fsml_options[customwidth]" value="<?php echo $options['customwidth']; ?>" /> (px if fixed, % if dynamic width)
					</td>
				</tr>
				<!-- Custom Z-index -->
				<tr class="apadvanced">
					<th scope="row">Custom Layering (CSS Z-Index)<br /><span style="color: #666666; margin-left: 10px">(determines which components of the page appear in front of or behind the floating frame; default: 9999)</span></th>
					<td>
						<input type="text" size="3" name="fsml_options[customzindex]" value="<?php echo $options['customzindex']; ?>" />
					</td>
				</tr>
			</table>
				
			<table class="form-table" id="fsml_design" >
				<!-- Themes -->
				<?php if(array_key_exists('theme',$options))
						$theme = $options['theme'];
					else
						$theme = 'custom';
				?>
				<tr id="themes" <?php if($theme == 'custom') echo 'style="display:none;"'; ?>><td colspan="2">
				<h3>Frame Themes</h3><p>Select a thumbnail to change the theme, then select "customize" to make any desired tweaks and see all options.</p>
				<ul id="themes-list">
					<li><h4>Bright</h4>
						<img src="<?php echo plugins_url('/img/admin/theme-bright.png',__FILE__); ?>" alt="bight theme" id="theme-bright" onclick="setTheme('bright')" <?php if($theme == 'bright') echo 'class="selected-theme"'; ?> /><br/>
						<a href="http://celloexpressions.com/stable/fsml/live.php?theme=bright" target="_blank" title="Website Using FSML with Bright Theme">See live example &raquo;</a>
					</li>
					<li><h4>Dark</h4>
						<img src="<?php echo plugins_url('/img/admin/theme-dark.png',__FILE__); ?>" alt="dark theme" id="theme-dark" onclick="setTheme('dark')" <?php if($theme == 'dark') echo 'class="selected-theme"'; ?> /><br/>
						<a href="http://celloexpressions.com/stable/fsml/live.php?theme=dark" target="_blank" title="Website Using FSML with Dark Theme">See live example &raquo;</a>
					</li>
					<li><h4>Elegant</h4>
						<img src="<?php echo plugins_url('/img/admin/theme-elegant.png',__FILE__); ?>" alt="elegant theme" id="theme-elegant" onclick="setTheme('elegant')" <?php if($theme == 'elegant') echo 'class="selected-theme"'; ?> /><br/>
						<a href="http://celloexpressions.com/stable/fsml/live.php?theme=elegant" target="_blank" title="Website Using FSML with Elegant Theme">See live example &raquo;</a>
					</li>
					<li><h4>Subtle</h4>
						<img src="<?php echo plugins_url('/img/admin/theme-subtle.png',__FILE__); ?>" alt="subtle theme" id="theme-subtle" onclick="setTheme('subtle')" <?php if($theme == 'subtle') echo 'class="selected-theme"'; ?> /><br/>
						<a href="http://celloexpressions.com/stable/fsml/live.php?theme=subtle" target="_blank" title="Website Using FSML with Subtle Theme">See live example &raquo;</a>
					</li>
					<li><h4>Contrast</h4>
						<img src="<?php echo plugins_url('/img/admin/theme-contrast.png',__FILE__); ?>" alt="contrast theme" id="theme-contrast" onclick="setTheme('contrast')" <?php if($theme == 'contrast') echo 'class="selected-theme"'; ?> /><br/>
						<a href="http://celloexpressions.com/stable/fsml/live.php?theme=contrast" target="_blank" title="Website Using FSML with Contrast Theme">See live example &raquo;</a>
					</li>
					<li><h4>Customize...</h4>
						<img src="<?php echo plugins_url('/img/admin/theme-custom.png',__FILE__); ?>" alt="custom theme" id="theme-customize" onclick="setTheme('custom')" <?php if($theme == 'custom') echo 'class="selected-theme"'; ?> /><br/>
						Show all options, with values set to those of the most recent theme.
					</li>
				</ul>
				</td></tr>
				<tr class="dadvanced"><td colspan="2"><h3 id="tobuiltin-theme"><a>Switch to Built-in Theme</a></h3></td></tr>
				<input type="hidden" name="fsml_options[theme]" id="current-theme" value="<?php echo $theme; ?>"/> 
				
				<!-- ColorScheme -->
				<tr class="dadvanced">
					<th scope="row" style="border-top: 1px solid #999;" >Choose a Color Scheme:</th>
					<td>
						<label><input id="fsmlcslight" name="fsml_options[colorscheme]" type="radio" value="light" <?php checked('light', $options['colorscheme']); ?> /> Light <span style="border: 2px solid #DDDDDD; box-shadow: 0 0 5px #000; background-color: #FFFFFF; color: #000; width: 40px; height: 20px; border-radius: 8px; padding: 0; margin-left: 10px;">   +++++++   </span><span style="color:#666666;margin-left:9px;"><i>(default)</i></span></label><br />
						<label><input id="fsmlcsdark" name="fsml_options[colorscheme]" type="radio" value="dark" <?php checked('dark', $options['colorscheme']); ?> /> Dark <span style="border: 2px solid #050505; box-shadow: 0 0 5px #101010; background-color: #202020; color: #fff; width: 40px; height: 20px; border-radius: 8px; padding: 0; margin-left: 10px;">   +++++++   </span></label><br />
						<label><input id="fsmlcscustom" name="fsml_options[colorscheme]" type="radio" value="custom" <?php checked('custom', $options['colorscheme']); ?> /> Use Custom Colors</label><br />
					</td>
				</tr>
				<tr class="dadvanced" >
					<td colspan="2"><table id="fsml_custom_colors" <?php if(fsml_897_getOption('colorscheme') != 'custom') { echo 'style="display: none;"'; } ?>>
					<?php	if( wp_style_is( 'wp-color-picker', 'registered' ) ) {
						$prewp3_5 = false;
					} else {
						$prewp3_5 = true;
					}?>
					<!-- Background Color -->
					<tr style="border-top: 1px solid #ddd;">
						<th scope="row">Frame Background Color</th>
						<td>
							<label><input type="text" class="fsml-color-picker dadvanced" name="fsml_options[backgroundcolor]" value="<?php echo $options['backgroundcolor']; ?>" /></label><?php if($prewp3_5)echo '<span style="color:#666666;margin-left:9px;">This version of WordPress doesn\'t support this color picker. Please update, or use 3/6 digit hexcolor codes. <br /></span>'; ?>
						</td>
					</tr>
					
					<!-- Border Color -->
					<tr id="bordercolor" <?php if($options['fixeddynamic'] == 'dynamic' || $options['border'] == 'no') { echo 'style="display: none;"'; } ?>>
						<th scope="row">Frame Border Color</th>	
						<td>
							<label><input type="text" class="fsml-color-picker" name="fsml_options[bordercolor]" value="<?php echo $options['bordercolor']; ?>" /></label><?php if($prewp3_5)echo '<span style="color:#666666;margin-left:9px;">This version of WordPress doesn\'t support this color picker. Please update, or use 3/6 digit hexcolor codes. <br /></span>'; ?>
						</td>
					</tr>
					
					<!-- Box Shadow Color -->
					<tr id="shadowcolor" <?php if($options['shadow'] == 'no') { echo 'style="display: none;"'; } ?>>
						<th scope="row">Frame Shadow Color</th>
						<td>
							<label><input name="fsml_options[frameshadow]" type="radio" value="black" <?php checked('black', $options['frameshadow']); ?> /> Black </label><br />
							<label><input name="fsml_options[frameshadow]" type="radio" value="white" <?php checked('white', $options['frameshadow']); ?> /> White </label><br />
						</td>
					</tr>
					
					<!-- Show/hide color -->
					<tr>
						<th scope="row">Show/Hide Button Color<br />
							<small>(only shows if visitors have the option to hide, see General Options page)</small>
						</th>
						<td>
							<label><input name="fsml_options[shbw]" type="radio" value="black" <?php checked('black', $options['shbw']); ?> /> Black </label><br />
							<label><input name="fsml_options[shbw]" type="radio" value="white" <?php checked('white', $options['shbw']); ?> /> White </label><br />
						</td>
					</tr>
					<!-- Facebook Like Color Scheme -->
					<tr>
						<th scope="row">Facebook Like Button Color Scheme<br />
							<small>(only shows up if the facebook like button is activated in Buttons)</small>
						</th>
						<td>
							<label><input name="fsml_options[fblikecolor]" type="radio" value="light" <?php checked('light', $options['fblikecolor']); ?> /> Light <span style="color:#666666;margin-left:9px;"><i>(default)</i></span></label><br />
							<label><input name="fsml_options[fblikecolor]" type="radio" value="dark" <?php checked('dark', $options['fblikecolor']); ?> /> Dark </label>
						</td>
					</tr>
					</table></td>
				</tr>

				<!-- Monochrome Icons -->
				<tr class="dadvanced">
					<th scope="row">Grayscale Icons</th>
					<td>
						<label><input name="fsml_options[grayscaleicons]" type="checkbox" value="1" <?php if (isset($options['grayscaleicons'])) { checked('1', $options['grayscaleicons']); } ?> /> Apply Grascale Filter to non-hovered icons (supported by <i>most, but not all</i> browsers, including IE 6-9)</label>
					</td>
				</tr>
				<!-- Frame Hover Effect -->
				<tr style="border-top: 1px solid #ddd;" class="dadvanced">
					<th scope="row">Frame Hover/Mouseover Effect</th>
					<td>
						<label><input name="fsml_options[framehovereffect]" type="radio" value="yes" <?php checked('yes', $options['framehovereffect']); ?> /> Fade/Unfade </label><br />
						<label><input name="fsml_options[framehovereffect]" type="radio" value="no" <?php checked('no', $options['framehovereffect']); ?> /> None </label>
					</td>
				</tr>
				<!-- Icon Hover Effect -->
				<tr class="dadvanced">
					<th scope="row">Icon Hover/Mouseover Effect</th>
					<td>
						<!-- Border Fade and Unfade option was buggy and has been removed -->
						<label><input name="fsml_options[hovereffect]" type="radio" value="yes" <?php checked('yes', $options['hovereffect']); ?> <?php checked('border', $options['hovereffect']); ?>/> Fade/Unfade </label><br />
						<label><input name="fsml_options[hovereffect]" type="radio" value="no" <?php checked('no', $options['hovereffect']); ?> /> None </label>
					</td>
				</tr>
				<!-- Border Radius -->
				<tr class="dadvanced" style="border-top: 1px solid #ddd;">
					<th scope="row">Rounded Corners</th>
					<td>
						<label><input name="fsml_options[borderradius]" type="radio" value="none" <?php checked('none', $options['borderradius']); ?> /> None </label><br />
						<label><input name="fsml_options[borderradius]" type="radio" value="small" <?php checked('small', $options['borderradius']); ?> /> Slight </label><br />
						<label><input name="fsml_options[borderradius]" type="radio" value="med" <?php checked('med', $options['borderradius']); ?> /> Medium </label><br />
						<label><input name="fsml_options[borderradius]" type="radio" value="wide" <?php checked('wide', $options['borderradius']); ?> /> Wide </label><br />
						<label id="custombropt"><input name="fsml_options[borderradius]" type="radio" value="custom" <?php checked('custom', $options['borderradius']); ?> /> Custom </label>
						<input type="text" size="3" name="fsml_options[custombr]" value="<?php echo $options['custombr']; ?>" />px (css border radius)
					</td>
				</tr>
				<!-- Show Box Shadow? -->
				<tr class="dadvanced">
					<th scope="row">Show Frame Shadow?</th>
					<td>
						<label id="shadowyes"><input name="fsml_options[shadow]" type="radio" value="yes" <?php checked('yes', $options['shadow']); ?> /> Yes </label><br />
						<label id="shadowno"><input name="fsml_options[shadow]" type="radio" value="no" <?php checked('no', $options['shadow']); ?> /> No </label><br />
					</td>
				</tr>
				<!-- Show Border? -->
				<tr id="showborder" <?php if($options['fixeddynamic'] == 'dynamic'){ echo 'style="display: none;"'; } ?> class="dadvanced">
					<th scope="row">Show Border Around Frame?</th>
					<td>
						<label id="borderyes"><input name="fsml_options[border]" type="radio" value="yes" <?php checked('yes', $options['border']); ?> /> Yes </label><br />
						<label id="borderno"><input name="fsml_options[border]" type="radio" value="no" <?php checked('no', $options['border']); ?> /> No </label>
					</td>
				</tr>
				<!-- Custom CSS -->
				<tr class="dadvanced">
					<th scope="row">Custom CSS</th>
					<td>
						<p>If you would like to add custom css to modify any plugin styling, place it here to ensure that it will override all other FSML CSS styles.</p>
						<textarea name="fsml_options[customcss]"><?php echo $options['customcss']; ?></textarea>
					</td>
				</tr>
			</table>
			
			<!--all variables stored as options need to be included on this page for wordpress to save them, even if they are only set in validation-->
			<div style="display: none">
				<label><input name="fsml_options[numactivecustom]" type="hidden" value="<?php echo $options['numactivecustom']; ?>" /></label>
				<label><input name="fsml_options[sitesubmitted]" id="sitesubmitted" type="hidden" value="<?php echo $options['sitesubmitted']; ?>" /></label>
				<label><input name="fsml_options[outputcss]" type="hidden" value="<?php // re-generate on each save ?>" /></label>
			</div>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Settings') ?>" />
			</p>
			<?php // no need for additional usage evaluations at this time. Continue storing the fact that it's been shared if it has been in order to facilitate potential future need for additional evaluation. //if($options['sitesubmitted']=='no'){ echo '<p><a href="http://celloexpressions.com/stable/fsml/submitsite.php?url='.urlencode(get_home_url()).'" target="blank" onclick="submitSiteDone()">If you enjoy this plugin, please submit your site to my list to consider for use in plugin screenshots by clicking this link. No data is recorded or tracked except for your website url.</a></p>'; }?>
		</form>

	</div>
	<?php	
} //options page

// Sanitize and validate input. Accepts an array, return a sanitized array.
function fsml_validate_options($input) {

		//reset invalid numeric inputs to defaults
	if (!is_numeric($input['cookielength'])) {
		$input['cookielength'] = '7';
	}
	if (!is_numeric($input['hidesize'])) {
		$input['hidesize'] = '600';
	}
	if (!is_numeric($input['customtop'])) {
		$input['customtop'] = '10';
		$input['topunits'] = 'px';
	}
	if (!is_numeric($input['custombr'])) {
		$input['custombr'] = '12';
	}
	if ($input['customzindex'] && !is_numeric($input['customzindex'])) {
		$input['customzindex'] = '9999';
	}
	
	//validate fixed/dynamic width options
	if ($input['fixeddynamic'] == 'dynamic') {
		if($input['size'] == 'fsmall')
			$input['size'] = 'dsmall';
		if($input['size'] == 'fnormal')
			$input['size'] = 'dnormal';
		if($input['size'] == 'flarge')
			$input['size'] = 'dlarge';
	}
	if ($input['fixeddynamic'] == 'fixed') {
		if($input['size'] == 'dsmall')
			$input['size'] = 'fsmall';
		if($input['size'] == 'dnormal')
			$input['size'] = 'fnormal';
		if($input['size'] == 'dlarge')
			$input['size'] = 'flarge';
	}
	
	//set colorscheme for facebook like
	if ($input['colorscheme'] == 'light') {
		$input['fblikecolor'] = 'light';
	}
	elseif ($input['colorscheme'] == 'dark') {
		$input['fblikecolor'] = 'dark';
	}
	
	//remove extra leading slashes
	if (substr($input['facebookurl'], 0, 1)=='/'){
		$input['facebookurl'] = substr($input['facebookurl'], 1);
	}
	if (substr($input['twitterurl'], 0, 1)=='/'){
		$input['twitterurl'] = substr($input['twitterurl'], 1);
	}
	if (substr($input['youtubeurl'], 0, 1)=='/'){
		$input['youtubeurl'] = substr($input['youtubeurl'], 1);
	}
	//remove unneeded http://s
	if (substr($input['facebookurl'], 0, 12)=='https://www.'){
		$input['facebookurl'] = substr($input['facebookurl'], 12);
	}
	if (substr($input['twitterurl'], 0, 7)=='http://'){
		$input['twitterurl'] = substr($input['twitterurl'], 7);
	}
	if (substr($input['youtubeurl'], 0, 11)=='http://www.'){
		$input['youtubeurl'] = substr($input['youtubeurl'], 11);
	}
	//remove unneeded facebook.coms
	if (substr($input['facebookurl'], 0, 13)=='facebook.com/'){
		$input['facebookurl'] = substr($input['facebookurl'], 13);
	}
	if (substr($input['twitterurl'], 0, 12)=='twitter.com/'){
		$input['twitterurl'] = substr($input['twitterurl'], 12);
	}
	if (substr($input['youtubeurl'], 0, 12)=='youtube.com/'){
		$input['youtubeurl'] = substr($input['youtubeurl'], 12);
	}
	//automatically add http://s to custom links, but only of /, http://, https://, mailto:, or sms: aren't present
	if(!(substr($input['customurl1'],0,7)=='http://' ||substr($input['customurl1'],0,8)=='https://' ||substr($input['customurl1'],0,1)=='/' ||substr($input['customurl1'],0,7)=='mailto:' ||substr($input['customurl1'],0,4)=='sms:'))
		$input['customurl1'] = 'http://' . $input['customurl1'];
	if(!(substr($input['customurl2'],0,7)=='http://' ||substr($input['customurl2'],0,8)=='https://' ||substr($input['customurl2'],0,1)=='/' ||substr($input['customurl2'],0,7)=='mailto:' ||substr($input['customurl2'],0,4)=='sms:'))
		$input['customurl2'] = 'http://' . $input['customurl2'];
	if(!(substr($input['customurl3'],0,7)=='http://' ||substr($input['customurl3'],0,8)=='https://' ||substr($input['customurl3'],0,1)=='/' ||substr($input['customurl3'],0,7)=='mailto:' ||substr($input['customurl3'],0,4)=='sms:'))
		$input['customurl3'] = 'http://' . $input['customurl3'];
	if(!(substr($input['customurl4'],0,7)=='http://' ||substr($input['customurl4'],0,8)=='https://' ||substr($input['customurl4'],0,1)=='/' ||substr($input['customurl4'],0,7)=='mailto:' ||substr($input['customurl4'],0,4)=='sms:'))
		$input['customurl4'] = 'http://' . $input['customurl4'];
	if(!(substr($input['customurl5'],0,7)=='http://' ||substr($input['customurl5'],0,8)=='https://' ||substr($input['customurl5'],0,1)=='/' ||substr($input['customurl5'],0,7)=='mailto:' ||substr($input['customurl5'],0,4)=='sms:'))
		$input['customurl5'] = 'http://' . $input['customurl5'];
	if(!(substr($input['customurl6'],0,7)=='http://' ||substr($input['customurl6'],0,8)=='https://' ||substr($input['customurl6'],0,1)=='/' ||substr($input['customurl6'],0,7)=='mailto:' ||substr($input['customurl6'],0,4)=='sms:'))
		$input['customurl6'] = 'http://' . $input['customurl6'];
	if(!(substr($input['customurl7'],0,7)=='http://' ||substr($input['customurl7'],0,8)=='https://' ||substr($input['customurl7'],0,1)=='/' ||substr($input['customurl7'],0,7)=='mailto:' ||substr($input['customurl7'],0,4)=='sms:'))
		$input['customurl7'] = 'http://' . $input['customurl7'];
	
	
	//reset the number of custom links that are active
	$numcustom = 0;
	if (fsml_validator_getoption('enablecustom1', $input)) { $numcustom++; }
	if (fsml_validator_getoption('enablecustom2', $input)) { $numcustom++; }
	if (fsml_validator_getoption('enablecustom3', $input)) { $numcustom++; }
	if (fsml_validator_getoption('enablecustom4', $input)) { $numcustom++; }
	if (fsml_validator_getoption('enablecustom5', $input)) { $numcustom++; }
	if (fsml_validator_getoption('enablecustom6', $input)) { $numcustom++; }
	if (fsml_validator_getoption('enablecustom7', $input)) { $numcustom++; }
	$input['numactivecustom'] = $numcustom;
	//ensure that the custom image urls are linked to image files, change them back to defaults if not
	if(!(substr($input['customurlimage1'], -3) == 'jpg' || substr($input['customurlimage1'], -3) == 'png' || substr($input['customurlimage1'], -3) == 'gif' || substr($input['customurlimage1'], -3) == 'svg' )) {
		$input['customurlimage1'] = plugins_url('img/blog-default.png', __FILE__);
	}if(!(substr($input['customurlimage2'], -3) == 'jpg' || substr($input['customurlimage2'], -3) == 'png' || substr($input['customurlimage2'], -3) == 'gif' || substr($input['customurlimage2'], -3) == 'svg' )) {
		$input['customurlimage2'] = plugins_url('img/more-default2.png', __FILE__);
	}if(!(substr($input['customurlimage3'], -3) == 'jpg' || substr($input['customurlimage3'], -3) == 'png' || substr($input['customurlimage3'], -3) == 'gif' || substr($input['customurlimage3'], -3) == 'svg' )) {
		$input['customurlimage3'] = plugins_url('img/more-default3.png', __FILE__);
	}if(!(substr($input['customurlimage4'], -3) == 'jpg' || substr($input['customurlimage4'], -3) == 'png' || substr($input['customurlimage4'], -3) == 'gif' || substr($input['customurlimage4'], -3) == 'svg' )) {
		$input['customurlimage4'] = plugins_url('img/more-default4.png', __FILE__);
	}if(!(substr($input['customurlimage5'], -3) == 'jpg' || substr($input['customurlimage5'], -3) == 'png' || substr($input['customurlimage5'], -3) == 'gif' || substr($input['customurlimage5'], -3) == 'svg' )) {
		$input['customurlimage5'] = plugins_url('img/more-default5.png', __FILE__);
	}if(!(substr($input['customurlimage6'], -3) == 'jpg' || substr($input['customurlimage6'], -3) == 'png' || substr($input['customurlimage6'], -3) == 'gif' || substr($input['customurlimage6'], -3) == 'svg' )) {
		$input['customurlimage6'] = plugins_url('img/more-default6.png', __FILE__);
	}if(!(substr($input['customurlimage7'], -3) == 'jpg' || substr($input['customurlimage7'], -3) == 'png' || substr($input['customurlimage7'], -3) == 'gif' || substr($input['customurlimage7'], -3) == 'svg' )) {
		$input['customurlimage7'] = plugins_url('img/more-default7.png', __FILE__);
	}
	
	$input['outputcss'] = fsml_options_css($input);
	
	return $input;
}

function fsml_validator_getoption($theoption, $thearray) {
	//if this check isn't made, php gets confused and 
	//sends an error message for each key whose value is null
	//so when "checkbox" options are unchecked, for example,
	//an error is thrown instead of false being returned.
	if(array_key_exists( $theoption, $thearray ))
		return $thearray[$theoption];
	else
		return false;
}

// Display a Settings link on the main Plugins page
function fsml_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$fsml_links = '<a href="'.get_admin_url().'options-general.php?page=floating-social-media-links%2Ffloating-social-media-links.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $fsml_links );
	}

	return $links;
}

function fsml_options_css( $options ) {
	// prepare the main plugin settings/options and output as css
	// set the styles for the various options (these aren't in external stylesheets because of the high level of variance on an option-by-option basis)
	// thus we avoid having 10 external stylesheets taking up diskspace and confusing condintionality including them
	// however we do increase the size of this file and the lenghth of its excecution time
	if (fsml_897_getOption('frameshadow',$options) == 'white') {
		$frameshadowcolor = '#fff';
	}
	else {
		$frameshadowcolor = '#000';
	}
	//set up colorschemes
	$thebackgroundcolor_897 = fsml_897_getOption('backgroundcolor',$options);
	$thebordercolor_897 = fsml_897_getOption('bordercolor',$options);	
	if (fsml_897_getOption('colorscheme',$options) == 'dark') {
		$thebackgroundcolor_897 = '#222';
		$thebordercolor_897 = '#050505';
		$frameshadowcolor = '#000';
	}
	if (fsml_897_getOption('colorscheme',$options) == 'light') {
		$thebackgroundcolor_897 = '#fff';
		$thebordercolor_897 = '#ddd';
		$frameshadowcolor = '#000';
	}
	//set up hover effect option output
	$fsmlhovereffectcss = '';
	if (fsml_897_getOption('hovereffect',$options) == 'no') {
		$fsmlhovereffectcss = 'img.fsml_fficon { opacity:1; } ';
	}
	elseif (fsml_897_getOption('hovereffect',$options) == 'border') {
		// deprecated
		$fsmlhovereffectcss = '';
	}
	elseif(fsml_897_getOption('hovereffect',$options) == 'yes') {
		// fade, but no border, already in base css by default
	}
	//prepare the vertical position
	$fsml_897_top = 0;
	$fsml_897_top_un = 'px';
	$fsml_897_top = fsml_897_getOption('customtop',$options);
	if(fsml_897_getOption('topunits',$options)) {
		$fsml_897_top_un = fsml_897_getOption('topunits',$options);
		if($fsml_897_top_un == 'pct') {
			$fsml_897_top_un = '%';
		}
	}
	//set up border radius
	switch(fsml_897_getOption('borderradius',$options)) {
		case('wide'): $brsize = '20px'; break;
		case('med'): $brsize = '12px'; break;
		case('small'): $brsize = '6px'; break;
		case('none'): $brsize = '0'; break;
		case('custom'): $brsize = fsml_897_getOption('custombr',$options).'px'; break;
		default:  $brsize = '12px'; break;
	}
	$fhmargin = '1%';
	//set up all 6 widths
	switch(fsml_897_getOption('size',$options)) {
		case 'fsmall': $framewidth = '43px'; $bmargin = '3px'; break;
		case 'fnormal': $framewidth = '60px'; $bmargin = '5px'; break;
		case 'flarge': $framewidth = '70px'; $bmargin = '7px'; break;
		case 'dsmall': $framewidth = '3.4%'; $bmargin = '3px'; $fhmargin = '.3%'; break;
		case 'dnormal': $framewidth = '4%'; $bmargin = '5px'; $fhmargin = '.5%'; break;
		case 'dlarge': $framewidth = '5.6%'; $bmargin = '7px'; $fhmargin = '.7%'; break;
	}	
	$fsmlcustomwidth_897 = fsml_897_getOption('customwidth',$options);
	if($fsmlcustomwidth_897){
		if(fsml_897_getOption('fixeddynamic',$options) == 'dynamic')
			$framewidth = $fsmlcustomwidth_897 . '%';
		else
			$framewidth = $fsmlcustomwidth_897 . 'px';
	}
	$css .= ('
		/*disclaimer: this css is php-generated, so while it isnt pretty here it does look fine where its generated*/'
		. $fsmlhovereffectcss . 
		'#fsml_ff, #fsml_ffhidden, #fsml_fblikemodal {
			background-color: ' . $thebackgroundcolor_897 . 
				'; border: 2px solid ' . $thebordercolor_897 . '; ');
		if (fsml_897_getOption('shadow',$options) != 'no') { $css .= ' box-shadow: 0 0 2px ' . $frameshadowcolor . '; '; }
		else { $css .= 'box-shadow: none;'; }
		if ( fsml_897_getOption('border',$options) != 'yes' || fsml_897_getOption('fixeddynamic',$options) == 'dynamic' ) { $css .= 'border: none;'; }
	$css .= '}';
	if(fsml_897_getOption('leftright',$options) == 'right'){
		$css .= ' #fsml_ff, #fsml_ffhidden { right: 0;  top: ' . $fsml_897_top . $fsml_897_top_un . ';}';
		$css .= ' #fsml_fblikemodal { left: -185px; }';
		$css .= '.fsml_xlr { right: 0; }';
		$css .= ' #fsml_ff { border-radius: ' . $brsize . '; ';
		if(fsml_897_getOption('closeoption',$options) != 'no'){
			$css .= 'border-top-right-radius: 0; ';
		}
		$css .= '}';
		// everything else is set to be on the right by default
	}
	else {
		$css .= ' #fsml_ff, #fsml_ffhidden { left: 0;  top: ' . $fsml_897_top . $fsml_897_top_un . ';}';
		$css .= ' #fsml_fblikemodal { right: -185px; }';
		$css .= ' #fsml_ff { border-radius: ' . $brsize . '; }';
		if(fsml_897_getOption('closeoption',$options) != 'no'){
			$css .= ' #fsml_ff { border-top-left-radius: 0; }';
		}
		$css .= '.fsml_xlr { left: 0; } #fsml_edit { right: 8px; }';
		$css .= ' #fsml_ffhidden { border-radius: 0; border-top-right-radius: 6px; border-bottom-right-radius: 6px; } ';
	}
	if( $brsize == 0 )
		$css .= '#fsml_ffmain img { border-radius: 0; }';
	
	//widths, other size-associated css
	$css .= '#fsml_ff { width: ' . $framewidth . '; margin: 0 ' . $fhmargin . '; } ';
	$css .= '.fsml_fflink img, #fsml_twfollow, img#fsml_ytsub { margin-bottom: ' . $bmargin . '; }';
	
	if (fsml_897_getOption('fblike',$options) && fsml_897_getOption('fbresize',$options)){ $css .= '#fsml_ff{ min-width: 61px; }'; }
	// target small screens (mobile devices or small desktop windows)
	if (fsml_897_getOption('hidefromsmall',$options)){
		$css .= '
			@media only screen and (max-width: ' . fsml_897_getOption('hidesize',$options) . 'px) {  
				/* hide the floating links frame on small mobile devices in case of overlap issues presented by some themes */
				#fsml_ff {display: none;}
				#fsml_ffhidden {display: none;}
		}';
	}
	if(fsml_897_getOption('framehovereffect',$options) == 'yes'){
		$css .= '#fsml_ff, 
		#fsml_ffhidden {
			opacity: 0.5;
		}
		#fsml_ff:hover, 
		#fsml_ffhidden:hover {
			opacity: 1;
		}';
	}
	if(fsml_897_getOption('grayscaleicons',$options)){
		$css .= '/* Grayscale Images */
		#fsml_ff img {
			filter: url('.plugins_url('filters.svg#grayscale',__FILE__).'); /* Firefox 3.5+ */
			filter: gray; /* IE6-9 */
			filter: grayscale(100%); /* Current draft standard */
			-webkit-filter: grayscale(1); /* Old Webkit */
			-webkit-filter: grayscale(100%); /* New WebKit */
			-moz-filter: grayscale(100%); /* Not yet supported in Gecko, Opera or IE */ 
			-ms-filter: grayscale(100%); /* no css solution for IE 10 */
			-o-filter: grayscale(100%); 
		}
		#fsml_ff img:hover {
			filter: none;
			-webkit-filter: grayscale(0);
			-moz-filter: grayscale(0);
			-ms-filter: grayscale(0);
			-o-filter: grayscale(0);
		}';
	}
	$css .= '/*custom css styling:*/
		'.fsml_897_getOption('customcss',$options);
	
	return $css;
}

//provides an easier way to safely get options
function fsml_897_getOption($the_option, $options = null) {
	if($options == null)
		$options = get_option('fsml_options');
	
	//if this check isn't made, wordpress gets confused and 
	//sends an error message for each key whose value is null
	//so when "checkbox" options are unchecked, for example,
	//an error is thrown instead of false being returned.
	//also, it is necessary to check for is_array first because wordpress gets to this during the initial activation process (before the option is set) for some reason...
	if(is_array($options) && array_key_exists( $the_option, $options ))
		return $options[$the_option];
	else
		return false;
}
	
//tell wordpress to call the output 
add_action('wp_head', 'fsml_output_head');
if(fsml_897_getOption('displaymethod') != 'custom') {
	add_action('wp_footer', 'fsml_output');
}
else {
	//Automatic Floating Social Media Links are currently disabled
}

//template tag used for custom output, with ability to pass up to 3 dynamic php-generated links
function floating_social_media_links($customphp1 = null, $customphp2 = null, $customphp3 = null){
	if(fsml_897_getOption('displaymethod') == 'custom')
		fsml_output($customphp1, $customphp2, $customphp3);
}

function fsml_output_head(){
//echo the html stylesheet embed code (enqueueing places them in the footer, which breaks the custom options)
//detailed appearance settings override these with css in <style> tag
	echo '<link rel="stylesheet" id="fsmlStyleSheet-1-5-1" href="' . plugins_url('fsml-base.css?ver=1.5.1', __FILE__) . '" type="text/css" media="all" />';
	if(fsml_897_getOption('metaviewport')) {
		// include html comment for easier tracing in case this is actually unwanted
		echo '<!-- From Floating Social Media Links: --><meta name="viewport" content="width=device-width" />';
	}
	echo '<style type="text/css">';
		if(fsml_897_getOption('outputcss') != null)
			echo fsml_897_getOption('outputcss');
		else
			echo fsml_options_css( get_option('fsml_options') );
	echo '</style>';
	
	if(fsml_897_getOption('ie7fix')){ ?>
		<!--[if lt IE 8]>
			<style type="text/css">
				.fsml_fficon { width: 50px; min-width: 100%; }
				.fsml_fficon:hover { width:100%; padding:0; }
			</style>
		<![endif]-->
	<?php }
}

function fsml_output($phplink1 = null, $phplink2 = null, $phplink3 = null){
	//set the outputs to vars, to avoid weird bugs with the Wordpress Settings API
		$fsmlbaseurl_897 = plugins_url('', __FILE__);
		$expandtitle = fsml_897_getOption('expandtitle');
		
		$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'sms', 'smsto' );
		
		$thefacebookurl_897 = fsml_897_getOption('facebookurl');
		$thefacebookurltitle_897 = fsml_897_getOption('facebookurltitle');
		$theyoutubeurl_897 = fsml_897_getOption('youtubeurl');
		$theyoutubeurltitle_897 = fsml_897_getOption('youtubeurltitle');
		$thetwitterurl_897 = fsml_897_getOption('twitterurl');
		$thetwitterurltitle_897 = fsml_897_getOption('twitterurltitle');
		
		$ytuseridnum = fsml_897_getOption('ytuseridnum');
		
		$thecustomurl1_897 = esc_url(fsml_897_getOption('customurl1'),$protocols);
		$thecustomurltitle1_897 = fsml_897_getOption('customurltitle1');
		$thecustomurlimage1_897 = fsml_897_getOption('customurlimage1');
		$thecustomurl2_897 = esc_url(fsml_897_getOption('customurl2'),$protocols);
		$thecustomurltitle2_897 = fsml_897_getOption('customurltitle2');
		$thecustomurlimage2_897 = fsml_897_getOption('customurlimage2');
		$thecustomurl3_897 = esc_url(fsml_897_getOption('customurl3'),$protocols);
		$thecustomurltitle3_897 = fsml_897_getOption('customurltitle3');
		$thecustomurlimage3_897 = fsml_897_getOption('customurlimage3');
		$thecustomurl4_897 = esc_url(fsml_897_getOption('customurl4'),$protocols);
		$thecustomurltitle4_897 = fsml_897_getOption('customurltitle4');
		$thecustomurlimage4_897 = fsml_897_getOption('customurlimage4');
		$thecustomurl5_897 = esc_url(fsml_897_getOption('customurl5'),$protocols);
		$thecustomurltitle5_897 = fsml_897_getOption('customurltitle5');
		$thecustomurlimage5_897 = fsml_897_getOption('customurlimage5');
		$thecustomurl6_897 = esc_url(fsml_897_getOption('customurl6'),$protocols);
		$thecustomurltitle6_897 = fsml_897_getOption('customurltitle6');
		$thecustomurlimage6_897 = fsml_897_getOption('customurlimage6');
		$thecustomurl7_897 = esc_url(fsml_897_getOption('customurl7'),$protocols);
		$thecustomurltitle7_897 = fsml_897_getOption('customurltitle7');
		$thecustomurlimage7_897 = fsml_897_getOption('customurlimage7');
		
		$fsml_897_xurl = '/img/x.png';
		$fsml_897_plusurl = '/img/plus.png';
		
		//override the custom link options for any custom dynamic links passed through floating_social_media_links()
		if(is_string($phplink1) && !empty($phplink1))//a string - ie not null, not empty
			$thecustomurl1_897 = $phplink1;
		if(is_string($phplink2) && !empty($phplink2))
			$thecustomurl2_897 = $phplink2;
		if(is_string($phplink3) && !empty($phplink3))
			$thecustomurl3_897 = $phplink3;

		//set up colorschemes
		if (fsml_897_getOption('colorscheme') == 'dark') {
			$fsml_897_xurl = '/img/x-dark.png';
			$fsml_897_plusurl = '/img/plusdark.png';
		}
		 elseif(fsml_897_getOption('colorscheme') == "custom" && fsml_897_getOption('shbw') == 'white') {
			$fsml_897_xurl = '/img/x-dark.png';
			$fsml_897_plusurl = '/img/plusdark.png';
		}


		// prepare the jquery for showing and hiding the frame
		$hsan = fsml_897_getOption('hsanimation');
		$hsuc = fsml_897_getOption('usecookies');
		$hscl = fsml_897_getOption('cookielength');
		wp_enqueue_script('fsmlhideshow', plugins_url("/fsml-hideshow.js", __FILE__), array('jquery'), false, true);

		$fblikecode = '';
		$fbmodalcode = '';
		if(fsml_897_getOption('fblike') ){
			if(fsml_897_getOption('fblikecolor') == 'dark') { $fblikecolorcode = 'dark'; } else { $fblikecolorcode = 'light'; }
			if(fsml_897_getOption('tolike') == 'site') { $thefacebookurltolike = site_url(); }
			else { $thefacebookurltolike = 'https://www.facebook.com/' . $thefacebookurl_897; } ?>
			<script>
				function fsml_openfblike(){
					jQuery('#fsml_fblikeframe').attr('src','<?php echo 'http://www.facebook.com/plugins/like.php?href='.urlencode($thefacebookurltolike).'&amp;layout=standard&amp;width=280&amp;show_faces=true&amp;font=arial&amp;colorscheme='.$fblikecolorcode.'&amp;action=like&amp;appId=123724601135059';?>'); // uses cello expressions app id
					jQuery('#fsml_fblikeframe').load(function(){
						jQuery('#fsml_fblikemodal').fadeIn(200);
					});
				}
				function fsml_closefblikemodal(){
					jQuery('#fsml_fblikemodal').fadeOut(100);
				}
		   </script>
		   <?php $fblikecode = '<img src="'.plugins_url('/img/like.png',__FILE__).'" alt="facebook like button" id="fsml_fblike" onclick="fsml_openfblike()" />';
			$fbmodalcode = '<div id="fsml_fblikemodal" style="display:none;">
				<iframe id="fsml_fblikeframe" ></iframe><hr/>
				<a href="javascript:void(0)" id="fsml_closefbmodal" onclick="fsml_closefblikemodal()">close</a>
			</div>';
		}
		
		$twfollowcode = '';
		if(fsml_897_getOption('twfollow') && $thetwitterurl_897 != ''){
			$twfollowcode = '<a href="https://twitter.com/intent/follow?screen_name=' . $thetwitterurl_897 . '" target="_blank"><img src="' . $fsmlbaseurl_897 . '/img/follow.png" alt="twitter follow button" id="fsml_twfollow" title="Follow @' . $thetwitterurl_897 . ' on Twitter"/></a>';
		}
		
		$ytsubcode = '';
		if(fsml_897_getOption('ytsub') && $ytuseridnum != ''){
			$ytsubcode = '<a href="//www.youtube.com/subscription_center?add_user_id=' . $ytuseridnum . '" target="_blank">' .
				'<img src="' . plugins_url('/img/subscribe.png', __FILE__) . '" alt="Subscribe on YouTube" id="fsml_ytsub" title="Subscribe to ' . $theyoutubeurl_897 . '\'s Youtube Channel" /></a>';
		}
		?>	
		<!--the floating frame-->
		<div id="fsml_ff" <?php if (fsml_897_getOption('closeoption') == 'starthidden') { echo 'style="display:none;"'; } if (fsml_897_getOption('customzindex')){ echo 'style="z-index: '.fsml_897_getOption('customzindex').';"'; } ?>>
			<?php if(fsml_897_getOption('closeoption') != 'no') { ?> <img src="<?php echo ($fsmlbaseurl_897 . $fsml_897_xurl); ?>" alt="close" class="fsml_xlr fsmlopenclose" id="fsml_hide" title="hide" onclick="fsmlHide(<?php echo "'$hsan','$hsuc','$hscl'"; ?>)" style="z-index: <?php if (fsml_897_getOption('customzindex')){ echo 1 + fsml_897_getOption('customzindex'); } else{ echo 10000; } ?>;" /> <?php } ?>
			<?php if(is_user_logged_in() && current_user_can('manage_options')) { echo '<a href="'.get_admin_url().'options-general.php?page=floating-social-media-links%2Ffloating-social-media-links.php"'.' id="fsml_edit" title="Edit Floating Links">edit</a>'; } ?>
			<?php echo $fbmodalcode; ?>
			<div id="fsml_ffmain">
			<?php if(fsml_897_getOption('displaymethod') == 'widgetbeta'){
				//display links with the beta/new/temporary method using the wordpress appearance->widgets page to allow reordering, etc.
				if ( ! dynamic_sidebar( 'Fsml Contents with Widgets TEMPORARY BETA' ) ) : 
						echo 'Error: the FSML Contents with Widgets TEMPORARY BETA is not installed.';
					 endif;
			}
			else {
			//display links with the standard method
				//standard links
				if (fsml_897_getOption('enablefacebook') && $thefacebookurl_897) { echo ('<a href="http://facebook.com/' . $thefacebookurl_897 . '" target="_blank" class="fsml_fflink"><img src="' . $fsmlbaseurl_897 . '/img/facebook.png" alt="Facebook Icon" title="' . $thefacebookurltitle_897 . '" class="fsml_fficon" /></a>'); }
				if (fsml_897_getOption('likelocs') == 'bylinks') { echo $fblikecode; }
				if (fsml_897_getOption('enableyoutube') && $theyoutubeurl_897) { echo ('<a href="http://youtube.com/' . $theyoutubeurl_897 . '" target="_blank" class="fsml_fflink"><img src="' . $fsmlbaseurl_897 . '/img/youtube.png" alt="YouTube Icon" title="' . $theyoutubeurltitle_897 . '" class="fsml_fficon" /></a>'); }
				if (fsml_897_getOption('likelocs') == 'bylinks') { echo $ytsubcode; }
				if (fsml_897_getOption('enabletwitter') && $thetwitterurl_897) { echo ('<a href="http://twitter.com/' . $thetwitterurl_897 . '" target="_blank" class="fsml_fflink"><img src="' . $fsmlbaseurl_897 . '/img/twitter.png" alt="Twitter Icon" title="' . $thetwitterurltitle_897 . '" class="fsml_fficon" /></a>'); }
				if (fsml_897_getOption('likelocs') == 'bylinks') { echo $twfollowcode; }
				//custom links - don't need to check for blank, because automatically must contain a min. of http:// ...
				if (fsml_897_getOption('enablecustom1')) { echo '<a href="' . $thecustomurl1_897 . '" '; if(fsml_897_getOption('customtarget1')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage1_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle1_897 . '" /></a>' ; } 
				if (fsml_897_getOption('enablecustom2')) { echo '<a href="' . $thecustomurl2_897 . '" '; if(fsml_897_getOption('customtarget2')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage2_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle2_897 . '" /></a>' ; } 
				if (fsml_897_getOption('enablecustom3')) { echo '<a href="' . $thecustomurl3_897 . '" '; if(fsml_897_getOption('customtarget3')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage3_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle3_897 . '" /></a>' ; }
				if (fsml_897_getOption('enablecustom4')) { echo '<a href="' . $thecustomurl4_897 . '" '; if(fsml_897_getOption('customtarget4')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage4_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle4_897 . '" /></a>' ; }
				if (fsml_897_getOption('enablecustom5')) { echo '<a href="' . $thecustomurl5_897 . '" '; if(fsml_897_getOption('customtarget5')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage5_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle5_897 . '" /></a>' ; } 
				if (fsml_897_getOption('enablecustom6')) { echo '<a href="' . $thecustomurl6_897 . '" '; if(fsml_897_getOption('customtarget6')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage6_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle6_897 . '" /></a>' ; }
				if (fsml_897_getOption('enablecustom7')) { echo '<a href="' . $thecustomurl7_897 . '" '; if(fsml_897_getOption('customtarget7')){ echo 'target="_blank"'; } echo ' class="fsml_fflink"><img src="' . $thecustomurlimage7_897 . '" class="fsml_fficon" alt="' . $thecustomurltitle1_897 . '" title="' . $thecustomurltitle7_897 . '" /></a>' ; }
				//buttons if all at the end
				if (fsml_897_getOption('likelocs') == 'afterall') { echo $fblikecode; echo $twfollowcode; echo $ytsubcode; }
			}?>
			</div>
		</div>
		<?php if (fsml_897_getOption('closeoption') == 'yes' || fsml_897_getOption('closeoption') == 'starthidden') { ?>
			<div id="fsml_ffhidden"  class="fsml_showhide" style="<?php if (fsml_897_getOption('closeoption') == 'starthidden' ) { echo 'display: block; '; } else { echo 'display: none; '; }  if (fsml_897_getOption('customzindex')){ echo 'z-index: '.fsml_897_getOption('customzindex').';'; } ?>">
				<img src="<?php echo $fsmlbaseurl_897 . $fsml_897_plusurl; ?>" alt="show" onclick="fsmlShow('<?php echo $hsan; ?>')" title="<?php echo $expandtitle; ?>" class="fsmlopenclose"/>
			</div>
		<?php }
} //fsml_output
?>