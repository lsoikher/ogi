<?php define("themename","iStudio");define("BROWSER_USER_AGENT",$_SERVER["HTTP_USER_AGENT"]);define("istudio_path",get_template_directory_uri());istudio_init();
function istudio_init(){
	if(function_exists('register_sidebar')){register_sidebar(array('name'=>themename.' Sidebar','description'=>themename.' RightSideBar','before_widget'=>'<ul><li id="%1$s" class="widget %2$s">','after_widget'=>'</li></ul>','before_title'=>'<h3 class="widgettitle">','after_title'=>'</h3>' ));}
	add_action('admin_menu','istudio_excerpt_meta_box');
	add_action('admin_menu',array('iStudioOptions','addOptions'));
	add_action('admin_menu','istudio_ShortLink_page');
	add_action('widgets_init',create_function('','return register_widget("istudio_RCommentsWidget");'));
	add_action('wp_before_admin_bar_render','istudio_themeopt_bar_render');
	add_action('wp_enqueue_scripts','istudio_enqueue_script');
	add_action('wp_head','istudio_headstyle');
	add_action('wp_footer','istudio_helloIe6_msg');
	add_editor_style();
	add_filter('excerpt_more','istudio_excerpt_more');
	add_filter('excerpt_length','istudio_excerpt_length');
	add_filter('wp_page_menu_args','istudio_homepage_menu_args');
	add_filter('widget_text','do_shortcode');
	add_shortcode('Downlink','istudio_downlink');
	add_shortcode('flv','istudio_flvlink');
	add_shortcode('mp3','istudio_mp3link');
	add_theme_support('automatic-feed-links');
	add_theme_support('custom-background');
	add_theme_support('post-thumbnails');
	register_nav_menu('istudio_navmenu','iStudio Navigation Menu');
	remove_action('wp_head','wp_generator');
	load_theme_textdomain('iStudio',TEMPLATEPATH.'/resources/languages');
	if(!isset($content_width))$content_width=600;
	add_theme_support('custom-header');
	register_default_headers(array('aurora'=>array('url'=>'%s/showpic/stushow1.jpg','thumbnail_url'=>'%s/showpic/stushow1_thumbnail.jpg','description'=>__('Aurora')),'beach'=>array('url'=>'%s/showpic/stushow2.jpg','thumbnail_url'=>'%s/showpic/stushow2_thumbnail.jpg','description'=>__('Beach')),'highway'=>array('url'=>'%s/showpic/stushow3.jpg','thumbnail_url'=>'%s/showpic/stushow3_thumbnail.jpg','description'=>__('Highway')),'train-graffiti'=>array('url'=>'%s/showpic/stushow4.jpg','thumbnail_url'=>'%s/showpic/stushow4_thumbnail.jpg','description'=>__('Train graffiti')),'sports-car'=>array('url'=>'%s/showpic/stushow5.jpg','thumbnail_url'=>'%s/showpic/stushow5_thumbnail.jpg','description'=>__('Sports car')),'cobblestone'=>array('url'=>'%s/showpic/stushow6.jpg','thumbnail_url'=>'%s/showpic/stushow6_thumbnail.jpg','description'=>__('Cobblestone')),'forest'=>array('url'=>'%s/showpic/stushow7.jpg','thumbnail_url'=>'%s/showpic/stushow7_thumbnail.jpg','description'=>__('Forest')),'save-me'=>array('url'=>'%s/showpic/stushow8.jpg','thumbnail_url'=>'%s/showpic/stushow8_thumbnail.jpg','description'=>__('Save Me')),'snow-leopard'=>array('url'=>'%s/showpic/stushow9.jpg','thumbnail_url'=>'%s/showpic/stushow9_thumbnail.jpg','description'=>__('Snow Leopard'))));
	define('NO_HEADER_TEXT',true);
	define('HEADER_TEXTCOLOR','fff');
	define('HEADER_IMAGE','');
	define('HEADER_IMAGE_WIDTH',apply_filters('iStudio_header_image_width',920));
	define('HEADER_IMAGE_HEIGHT',apply_filters('iStudio_header_image_height',160));
	add_theme_support('custom-header',array('random-default'=>true));
}
function istudio_is_ie(){return strpos(BROWSER_USER_AGENT,'MSIE');}
function istudio_is_ie9(){return strpos(BROWSER_USER_AGENT,'MSIE 9');}
function istudio_is_ie10(){return strpos(BROWSER_USER_AGENT,'MSIE 10');}
function istudio_themeopt_bar_render(){
	global $wp_admin_bar;
	$wp_admin_bar->add_menu(array('parent'=>false,'id'=>themename.' '.__('Options','iStudio'),'title'=>themename.' '.__('Options','iStudio'),'href'=>admin_url('themes.php?page=istudio_options'),'meta'=>false));
	$wp_admin_bar->add_menu(array('parent'=>false,'id'=>__('iStudio Shortcode','iStudio'),'title'=>__('iStudio Shortcode','iStudio'),'href'=>admin_url('themes.php?page=istudio_shortcode'),'meta'=>false));
}
function istudio_excerpt_meta_box(){add_meta_box('postexcerpt',__('Excerpt'),'post_excerpt_meta_box','page','normal','core');}
function istudio_excerpt_length($length){return 255;}
function istudio_excerpt_more($more){return '...';}
function istudio_homepage_menu_args($args){$args['show_home']=true;return $args;}
function istudio_rcomments($limit){$comments=get_comments(array('number'=>100,'status'=>'approve'));$wpchres=get_option('blog_charset');$i=1;$istorcoutput='';foreach($comments as $comment){$istorcoutput.='<li><a href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'" title="On '.get_the_title($comment->comment_post_ID).'">'.stripslashes($comment->comment_author).'</a>: '.mb_substr(strip_tags($comment->comment_content),0,32,$wpchres).'...</li>'."\n";if($i==$limit){break;}$i++;}echo ent2ncr($istorcoutput);}
function istudio_RandomShow($rsnum){if(!$rsnum){$rsnum=3;}$num=rand(1,$rsnum);$random='show'.$num;return $random;}
function istudio_page_number(){global $paged;if($paged>=2)echo ' - '.sprintf('Page %s',$paged);}
// Random images show.
function istudio_randompic(){echo '<div id="RandomShow">'."\n";if(get_header_image()){echo '<img src="'.get_header_image().'" width="'.HEADER_IMAGE_WIDTH.'" height="'.HEADER_IMAGE_HEIGHT.'" alt="" />'."\n";}else{echo '<img src="'.istudio_path.'/showpic/stu'.istudio_RandomShow(9).'.jpg" width="'.HEADER_IMAGE_WIDTH.'" height="'.HEADER_IMAGE_HEIGHT.'" alt="" />'."\n";}echo '</div>'."\n";}
// Enqueue Script.
function istudio_enqueue_script(){
	if((istudio_is_ie())&&(!istudio_is_ie9())&&(!istudio_is_ie10()))wp_enqueue_script('html5shiv','http://html5shiv.googlecode.com/svn/trunk/html5.js');
	wp_enqueue_script('jquery');
	wp_enqueue_script('theme_scripts',istudio_path.'/resources/scripts/scripts.js',array('jquery'),false,true);
	if(istOption('GrowlSwitch')&&istOption('GrowlInfo')){wp_enqueue_script('jgrowl',istudio_path.'/resources/scripts/jquery.jgrowl.js',array('jquery'),false,true);}
	if(is_single())wp_enqueue_script('prettify',istudio_path.'/resources/prettify/prettify.js',array('jquery'),false,true);
	if(is_singular()&& get_option('thread_comments'))wp_enqueue_script('comment-reply',array('jquery'),false,true);
}
function istudio_headstyle(){
	if(istOption('GrowlSwitch')&&istOption('GrowlInfo')){echo "<link rel=\"stylesheet\" href=\"".istudio_path."/resources/scripts/jquery.jgrowl.css\" type=\"text/css\"/>\n";}
	if(is_single()){echo "<link rel=\"stylesheet\" href=\"".istudio_path."/resources/prettify/prettify.css\" type=\"text/css\"/>\n";}
}
function istudio_custom_feed(){if(istOption('CustomFeed')&&istOption('CustomRssUrl')){remove_action('wp_head', 'feed_links_extra');$CustomFeed=istOption('CustomRssUrl');echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".get_bloginfo('name')." RSS Feed\" href=\"".$CustomFeed."\" />\n";}}
// Custom Shortcode.
function istudio_downlink($atts,$content=null){extract(shortcode_atts(array("href"=>'http://'),$atts));return '<div class="but_down"><a href="'.$href.'"target="_blank"><span>'.$content.'</span></a><div class="clear"></div></div>';}
function istudio_flvlink($atts,$content=null){extract(shortcode_atts(array("auto"=>'0'),$atts));
if(substr($content,-4)=='.mp4'){$videoSource='<source src="'.$content.'" type="video/mp4" />';}
if($auto){$videoAuto=' autoplay="autoplay"';}else{$videoAuto='';}$videoTag='<video width="560" height="315" controls="controls"'.$videoAuto.'>';
return $videoTag.$videoSource.'<embed src="'.istudio_path.'/resources/flvideo.swf?auto='.$auto.'&flv='.$content.'" menu="false" quality="high" wmode="transparent" bgcolor="#ffffff" width="560" height="315" name="flvideo" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer_cn" /></video>';}
function istudio_mp3link($atts, $content=null){extract(shortcode_atts(array("auto"=>'0',"replay"=>'0',),$atts));
if($auto){$audioautpay=' autoplay="autoplay" ';}else{$audioautpay='';}if($replay){$audioloop=' loop="loop"';}else{$audioloop='';}
$audioTag='<audio controls="controls"'.$audioautpay.$audioloop.'>';
return $audioTag.'<source src="'.$content.'" type="audio/mpeg" /><embed src="'.istudio_path.'/resources/dewplayer.swf?mp3='.$content.'&amp;autostart='.$auto.'&amp;autoreplay='.$replay.'" wmode="transparent" height="20" width="200" type="application/x-shockwave-flash" /></audio>';}
// Themes Options Page.
function istOption($option){$options=get_option('iStudio_Options');if(($option=='SearchKeyword')or($option=='pagenavnum')or($option=='CustomRssUrl')or($option=='GrowlInfo')or($option=='googleId')or($option=='widgetInfo')or($option=='adfloat')or($option=='adcode_singlecont')or($option=='adcode_single')or($option=='adcode_sidebar')){return ent2ncr($options[$option]);}else{return esc_attr($options[$option]);}}
class iStudioOptions{
	function getOptions(){$options=get_option('iStudio_Options');if(!is_array($options)){$options['imageLogo']=false;$options['SearchKey']=false;$options['SearchKeyword']='';$options['pagenavnum']='theme';$options['showside']=false;$options['pageside']=false;$options['CustomFeed']=false;$options['CustomRssUrl']='';$options['GrowlSwitch']=false;$options['GrowlInfo']='';$options['googleSearch']=false;$options['googleId']='';$options['ctrlentry']=false;$options['recommend_widget']=false;$options['widgetInfo']='';$options['adfloat']='left';$options['ad_show_singlecont']=false;$options['ad_show_single']=false;$options['ad_show_sidebar']=false;$options['ad_show_rShow']=false;$options['adcode_singlecont']='';$options['adcode_single']='';$options['adcode_sidebar']='';update_option('iStudio_Options',$options);}return $options;}
	function resOptions(){delete_option('iStudio_Options');}
	function addOptions(){if(isset($_POST['save_submit'])){$options=iStudioOptions::getOptions();if(@$_POST['imageLogo']){$options['imageLogo']=(bool)true;}else{$options['imageLogo']=(bool)false;}if(@$_POST['SearchKey']){$options['SearchKey']=(bool)true;}else{$options['SearchKey']=(bool)false;}$options['SearchKeyword']=stripslashes($_POST['SearchKeyword']);$options['pagenavnum']=stripslashes($_POST['pagenavnum']);if(@$_POST['showside']){$options['showside']=(bool)true;}else{$options['showside']=(bool)false;}if(@$_POST['pageside']){$options['pageside']=(bool)true;}else{$options['pageside']=(bool)false;}if(@$_POST['CustomFeed']){$options['CustomFeed']=(bool)true;}else{$options['CustomFeed']=(bool)false;}$options['CustomRssUrl']=stripslashes($_POST['CustomRssUrl']);if(@$_POST['GrowlSwitch']){$options['GrowlSwitch']=(bool)true;}else{$options['GrowlSwitch']=(bool)false;}$options['GrowlInfo']=stripslashes($_POST['GrowlInfo']);if(@$_POST['googleSearch']){$options['googleSearch']=(bool)true;}else{$options['googleSearch']=(bool)false;}$options['googleId']=stripslashes($_POST['googleId']);if(@$_POST['recommend_widget']){$options['recommend_widget']=(bool)true;}else{$options['recommend_widget']=(bool)false;}$options['widgetInfo']=stripslashes($_POST['widgetInfo']);if(@$_POST['ctrlentry']){$options['ctrlentry']=(bool)true;}else{$options['ctrlentry']=(bool)false;}$options['adfloat']=stripslashes($_POST['adfloat']);if(@$_POST['ad_show_singlecont']){$options['ad_show_singlecont']=(bool)true;}else{$options['ad_show_singlecont']=(bool)false;}if(@$_POST['ad_show_single']){$options['ad_show_single']=(bool)true;}else{$options['ad_show_single']=(bool)false;}if(@$_POST['ad_show_sidebar']){$options['ad_show_sidebar']=(bool)true;}else{$options['ad_show_sidebar']=(bool)false;}if(@$_POST['ad_show_rShow']){$options['ad_show_rShow']=(bool)true;}else{$options['ad_show_rShow']=(bool)false;}$options['adcode_singlecont']=stripslashes($_POST['adcode_singlecont']);$options['adcode_single']=stripslashes($_POST['adcode_single']);$options['adcode_sidebar']=stripslashes($_POST['adcode_sidebar']);update_option('iStudio_Options',$options);echo "<div id='message' class='updated fade'><p><strong>".__('Settings saved.','iStudio')."</strong></p></div>";}else{iStudioOptions::getOptions();}if(isset($_REQUEST['restore-defaults'])){iStudioOptions::resOptions();echo "<div id='message' class='updated fade'><p><strong>".__('Settings have been restored to default.','iStudio')."</strong></p></div>";}
	add_theme_page(themename.' '.__('Options','iStudio'),themename.' '.__('Options','iStudio'),'edit_themes','istudio_options',array('iStudioOptions','OptionsPage'));}
	function OptionsPage(){$options=iStudioOptions::getOptions();?>
<script type="text/javascript">(function(jQuery){istojQ=jQuery.noConflict();istojQ(document).ready(function(){istojQ('.iStudio_fold').find('.iSfold').hide();istojQ('.iStudio_fold').find('legend').mouseup(function(){var answer=istojQ(this).next();if(answer.is(':visible')){answer.slideUp();}else{answer.slideDown();}});});})(jQuery);</script>
<style type="text/css">.wrap{padding:10px;}fieldset{border:1px solid #ddd;margin:5px 0 10px;padding:5px 15px 15px;border-radius:5px;}fieldset:hover{border-color:#bbb;}fieldset legend{padding:0 5px;color:#666;font-size:14px;font-weight:700;}fieldset .line{border-bottom:1px solid #e5e5e5;padding-bottom:15px;}#isdonate{float:right;position:absolute; background:#e6ffe2;right:25px;top:25px;border:1px solid #b4ccaf;border-radius:5px;padding:5px 9px 0;width:250px;color:#000;}#isdonate p{margin:0;}#isdonate form img{display:none;}.wrap div.updated, div.error{margin-right:300px;}</style>
<div class="wrap">
  <div id="icon-options-general" class="icon32"></div>
  <h2><?php echo themename.' ';_e('Options','iStudio');?></h2>
  <div id="isdonate">
  	<p><?php _e('If you very nice this theme, you can click this button to donate to me.','iStudio');?></p>
  	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="4839415"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypal.com/zh_XC/i/scr/pixel.gif" width="1" height="1"></form>
  </div>
	<p><?php _e('Thank you for using iStudio. This Theme is Designed by Xu.hel.','iStudio');?></p>
	<p><?php printf(__('Theme Logo PSD file here: <a href="%1$s" target="_blank">Download Logo PSD</a>.','iStudio'),esc_url(__('http://xuhelblog.googlecode.com/files/iStudio_logo_psd.tar.gz','iStudio')));?></p>
  <div>
  <form action="#" method="post" enctype="multipart/form-data" name="options_form" id="options_form" class="iStudio_fold">
    <fieldset>
      <legend><?php _e('General','iStudio');?></legend>
      <div style="padding-left:1em;">
        <p><?php _e('Page Number style is displayed as:','iStudio');?><label style="margin-right:20px;"><input name="pagenavnum" type="radio" value="theme" <?php if($options['pagenavnum']!='default')echo "checked='checked'";?> /><?php _e('Theme Styles','iStudio');?></label><label><input name="pagenavnum" type="radio" value="default" <?php if($options['pagenavnum']=='default')echo "checked='checked'";?> /><?php _e('Default Styles','iStudio');?></label></p>
        <p><label><input name="imageLogo" type="checkbox" value="checkbox" <?php if($options['imageLogo'])echo "checked='checked'";?> > <?php _e('Use the Picture Logo.','iStudio');?></label></p>
        <p><label><input name="showside" type="checkbox" value="checkbox" <?php if($options['showside'])echo "checked='checked'";?> /> <?php _e('Show sidebar in single pages.','iStudio');?></label></p>
        <p><label><input name="pageside" type="checkbox" value="checkbox" <?php if($options['pageside'])echo "checked='checked'";?> /> <?php _e('Show sidebar in pages.','iStudio');?></label></p>
        <p><label><input name="ctrlentry" type="checkbox" value="checkbox" <?php if($options['ctrlentry'])echo "checked='checked'";?> /> <?php _e('Use Ctrl+Enter to Reply Comments.','iStudio');?></label></p>
      </div>
    </fieldset>
    <fieldset>
      <legend><label><input name="SearchKey" type="checkbox" value="checkbox" <?php if($options['SearchKey'])echo "checked='checked'";?> /> <?php _e('Enable keywords for search engine.','iStudio');?></label></legend>
      <div<?php if(!$options['SearchKey'])echo ' class="iSfold"';?> style="padding-left:1em;">
        <p><?php _e('Search engine keywords values fill in the following input box. Each keyword with "," separated.','iStudio');?></p>
        <input name="SearchKeyword" type="text" value="<?php echo($options['SearchKeyword']);?>" size="80" />
      </div>
    </fieldset>
    <fieldset>
      <legend><label><input name="CustomFeed" type="checkbox" value="checkbox" <?php if($options['CustomFeed'])echo "checked='checked'";?> > <?php _e('Custom RSS Subscription','iStudio');?></label></legend>
      <div<?php if(!$options['CustomFeed'])echo ' class="iSfold"';?> style="padding-left:1em;">
        <p><?php _e('Please enter a new RSS subscription address (if not set is to use WordPress\'s default subscription address).','iStudio');?></p>
        <input name="CustomRssUrl" type="text" value="<?php echo($options['CustomRssUrl']);?>" size="80" />
      </div>
    </fieldset>
    <fieldset>
      <legend><label><input name="GrowlSwitch" type="checkbox" value="checkbox" <?php if($options['GrowlSwitch'])echo "checked='checked'";?> > <?php _e('Enable GrowlBox pop-up tips','iStudio');?></label></legend>
      <div<?php if(!$options['GrowlSwitch'])echo ' class="iSfold"';?> style="padding-left:1em;">
        <p><?php _e('GrowlBox the content of the pop-up tips(Use HTML code.):','iStudio');?></p>
        <input name="GrowlInfo" type="text" value="<?php echo(esc_html($options['GrowlInfo']));?>" size="80" />
      </div>
    </fieldset>
    <fieldset>
      <legend><label><input name="googleSearch" type="checkbox" value="checkbox" <?php if($options['googleSearch'])echo "checked='checked'";?> /> <?php _e('Use google custom search engine','iStudio');?></label></legend>
      <div<?php if(!$options['googleSearch'])echo ' class="iSfold"';?> style="padding-left:1em;">
        <p><?php printf(__('Find <code>name="cx"</code> in the <strong>Search box code</strong> of <a href="%1$s">Google Custom Search Engine</a>, and enter the <code>value</code> here.<br/>For example: <code>014782006753236413342:1ltfrybsbz4</code>','iStudio'),esc_url(__('http://www.google.com/coop/cse/','iStudio')));?></p>
        <input name="googleId" type="text" value="<?php echo($options['googleId']);?>" size="80" />
      </div>
    </fieldset>
    <fieldset>
      <legend><label><input name="recommend_widget" type="checkbox" value="checkbox" <?php if($options['recommend_widget'])echo "checked='checked'";?> /> <?php _e('Open the article Recommendation Widgets','iStudio');?></label></legend>
      <div<?php if(!$options['recommend_widget'])echo ' class="iSfold"';?> style="padding-left:1em;">
        <p><?php _e('Recommended articles to be linked with the HTML code written in the input box below:','iStudio');?></p>
        <textarea name="widgetInfo" cols="80" rows="3"><?php echo($options['widgetInfo']);?></textarea>
      </div>
    </fieldset>
    <fieldset>
      <legend><?php _e('Ad Code Settings','iStudio');?></legend>
      <div>
      <div style="padding-left:1em;" class="line">
        <p><?php _e('Ads code will not be displayed as long as the ad code box was emptied.','iStudio');?></p>
        <p><?php _e('Ad code embedded in the article: (Max size: 300x250)','iStudio');?></p>
        <p style="text-indent:2em;"><?php _e('Embedded ads appear here:','iStudio');?><label style="margin-right:20px;"><input name="adfloat" type="radio" value="left" <?php if($options['adfloat']!='right')echo "checked='checked'";?> /><?php _e('left','iStudio');?></label><label><input name="adfloat" type="radio" value="right" <?php if($options['adfloat']=='right')echo "checked='checked'";?> /><?php _e('right','iStudio');?></label><label style="margin-left:20px;"><input name="ad_show_singlecont" type="checkbox" value="checkbox" <?php if($options['ad_show_singlecont'])echo "checked='checked'";?> /> <?php _e('Hide after user logged in','iStudio');?></label></p>
        <textarea name="adcode_singlecont" cols="80" rows="3"><?php echo($options['adcode_singlecont']);?></textarea>
      </div>
      <div style="padding-left:1em;" class="line">
        <p><?php _e('Ad code displayed inside single article: (Max size: 728x90)','iStudio');?><label><input name="ad_show_single" type="checkbox" value="checkbox" <?php if($options['ad_show_single'])echo "checked='checked'";?> /> <?php _e('Hide after user logged in','iStudio');?></label></p>
        <textarea name="adcode_single" cols="80" rows="3"><?php echo($options['adcode_single']);?></textarea>
      </div>
      <div style="padding-left:1em;">
        <p><?php _e('Ad code displayed in the sidebar: (Max size: 200x220)','iStudio');?><label><input name="ad_show_sidebar" type="checkbox" value="checkbox" <?php if($options['ad_show_sidebar'])echo "checked='checked'";?> /> <?php _e('Hide after user logged in','iStudio');?></label></p>
        <textarea name="adcode_sidebar" cols="80" rows="3"><?php echo($options['adcode_sidebar']);?></textarea>
      </div>
      </div>
    </fieldset>
    <p class="submit">
      <input type="submit" name="save_submit" class="button-primary" value="<?php _e('Save Changes','iStudio');?>" />
      <input name="restore-defaults" id="restore-defaults" onClick="return confirmDefaults();" value="<?php _e('Revert to Defaults','iStudio');?>" class="button-secondary" type="submit">
    </p>
  </form>
  </div>
  </fieldset>
</div>
<?php }
}
function istudio_get_logo(){$logoImage=istOption('imageLogo');if($logoImage){?>
  <h1 class="hidden"><a href="<?php echo home_url('/');?>"><?php bloginfo('name');?></a></h1>
  <div class="hidden"><?php bloginfo('description');?></div>
  <a class="logo" href="<?php echo home_url('/');?>"></a>
  <?php }else{?>
  <h1><a href="<?php echo home_url('/');?>"><?php bloginfo('name');?></a></h1>
  <div class="description"><?php bloginfo('description');?></div>
  <?php }
}
function istudio_Shortpage(){?>
<style type="text/css">fieldset{border:1px solid #ddd;margin:5px 0 10px;padding:0 15px;-moz-border-radius:5px;-khtml-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;}fieldset:hover{border-color:#bbb;}fieldset legend{padding:0 5px;font-size:14px;}</style>
<div class="wrap">
<div id="icon-upload" class="icon32"><br></div>
<h2><?php _e('iStudio Shortcode','iStudio');?></h2>
<fieldset>
  	<legend><?php _e('iStudio Custom Shortcode format','iStudio');?></legend>
    <div style="padding-left:1em;">
    	<h4><?php _e('Insert Download','iStudio');?></h4>
      <p><?php _e('Insert the following code into the editor, you will be able to use the built-in download button style.','iStudio');?></p>
      <p><?php _e('Code format: <code>[Downlink href="http://www.xxx.com/xxx.zip"]download xxx.zip[/Downlink]</code>','iStudio');?></p>
      <h4><?php _e('Insert MP3 music','iStudio');?></h4>
      <p><?php _e('Insert the following code into the editor, you will be able to use the built-in MP3 music player..','iStudio');?></p>
      <p><?php _e('Only supports .mp3 file URL. If the browser supports html5 then automatically <audio> tag.','iStudio');?></p>
      <p><?php _e('Code format: <code>[mp3]http://www.xxx.com/xxx.mp3[/mp3]</code>','iStudio');?></p>
      <p><?php _e('Auto Play: <code>[mp3 auto="1"]http://www.xxx.com/xxx.mp3[/mp3]</code>','iStudio');?></p>
      <p><?php _e('Repeat Play: <code>[mp3 replay="1"]http://www.xxx.com/xxx.mp3[/mp3]</code>','iStudio');?></p>
      <p><?php _e('Auto Play and Repeat Play: <code>[mp3 auto="1" replay="1"]http://www.xxx.com/xxx.mp3[/mp3]</code>','iStudio');?></p>
      <h4><?php _e('Insert FLV video','iStudio');?></h4>
      <p><?php _e('Insert the following code into the editor, you will be able to use the built-in video player to play FLV video.','iStudio');?></p>
      <p><?php _e('Only supports .mp4 file URL. If the browser supports html5 then automatically <video> tag.','iStudio');?></p>
      <p><?php _e('Code format: <code>[flv]http://www.xxx.com/xxx.flv[/flv]</code>','iStudio');?></p>
      <p><?php _e('Auto Play: <code>[flv auto="1"]http://www.xxx.com/xxx.flv[/flv]</code>','iStudio');?></p>
    </div>
  </fieldset>
</div>
<?php }
function istudio_ShortLink_page(){add_theme_page(__('iStudio Shortcode','iStudio'),__('iStudio Shortcode','iStudio'),'edit_posts','istudio_shortcode','istudio_Shortpage');}
// Custom Comments List.
if(function_exists('wp_list_comments')){
	function comment_count($commentcount){global $id;$_commnets=get_comments('status=approve&post_id='.$id);$comments_by_type=&separate_comments($_commnets);return count($comments_by_type['comment']);}
}
function istudio_custom_comments($comment,$args,$depth){
	$GLOBALS['comment']=$comment;
	switch($comment->comment_type){
		case '':?>
	<li <?php comment_class();?> id="li-comment-<?php comment_ID();?>">
    <?php if($comment->comment_author_email==get_the_author_meta('email')){?>
		<div id="comment-<?php comment_ID();?>" class="list children">
    <?php echo get_avatar($comment,32);?>
    <table class="bubble" border="0" cellpadding="0" cellspacing="0">
      <tr><td class="topleft"></td><td class="top"></td><td class="topright"></td></tr>
      <tr><td class="left"></td>
        <td align="left" class="comment-body"><?php comment_text();?></td>
        <td class="right"></td></tr>
      <tr><td class="bottomleft"></td><td class="bottom"></td><td class="bottomright"></td></tr>
    </table>
    <div class="comment-author">
			<?php printf(__('%1$s at %2$s <span class="says"></span>','iStudio'),sprintf('<cite class="fn">%s</cite>',get_comment_author_link()),sprintf('<small class="comment-meta"><a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a></small>',esc_url(get_comment_link($comment->comment_ID)),get_comment_time('c'),sprintf(__('%1$s %2$s','iStudio'),get_comment_date('Y-m-d'),get_comment_time(' H:i'))));
			if($comment->comment_approved=='0'){?><em><?php _e('Your comment is awaiting moderation.','iStudio');?></em><?php }
			edit_comment_link('Edit','&nbsp;&nbsp;',' | ');comment_reply_link(array_merge($args,array('depth'=>$depth,'max_depth'=>$args['max_depth'])));?>
		</div>
	</div>
	<?php }else{?>
	<div id="comment-<?php comment_ID();?>" class="list">
		<?php echo get_avatar($comment,32);?>
    <table class="bubble" border="0" cellpadding="0" cellspacing="0">
      <tr><td class="topleft"></td><td class="top"></td><td class="topright"></td></tr>
      <tr><td class="left"></td>
        <td align="left" class="comment-body"><?php comment_text();?></td>
        <td class="right"></td></tr>
      <tr><td class="bottomleft"></td><td class="bottom"></td><td class="bottomright"></td></tr>
    </table>
    <div class="comment-author">
			<?php printf(__('%1$s at %2$s <span class="says"></span>','iStudio'),sprintf('<cite class="fn">%s</cite>',get_comment_author_link()),sprintf('<small class="comment-meta"><a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a></small>',esc_url(get_comment_link($comment->comment_ID)),get_comment_time('c'),sprintf(__('%1$s %2$s','iStudio'),get_comment_date('Y-m-d'),get_comment_time(' H:i'))));
			if($comment->comment_approved=='0'){?><em><?php _e('Your comment is awaiting moderation.','iStudio');?></em><?php }
			edit_comment_link('Edit','&nbsp;&nbsp;',' | ');comment_reply_link(array_merge($args,array('depth'=>$depth,'max_depth'=>$args['max_depth'])));?>
		</div>
	</div>
	<?php }?>
	<?php break;
		case 'pingback':
		case 'trackback':?>
	<li class="post pingback">
		<p><?php _e('Pingback:','iStudio');comment_author_link();edit_comment_link(__('Edit','iStudio'),' <span class="edit-link">[',']</span>');?></p>
	<?php break;
	}
}
// Hello IE6 User.
function istudio_helloIe6_msg(){if(istudio_is_ie()){?>
<!--[if lte IE 7]>
<script type="text/javascript">var IE6UPDATE_OPTIONS={icons_path:"<?php echo istudio_path.'/resources/scripts/ie6upimg/';?>"}</script>
<script type="text/javascript" src="<?php echo istudio_path.'/resources/scripts/ie6update.js';?>"></script>
<![endif]-->
<?php }}
// Page Nav.
function istudio_pagenavi(){global $wp_query;$options=array('pages_text'=>'Page %CURRENT_PAGE% of %TOTAL_PAGES%','current_text'=>'%PAGE_NUMBER%','page_text'=>'%PAGE_NUMBER%','prev_text'=>'&laquo;','next_text'=>'&raquo;','dotleft_text'=>'...','dotright_text'=>'...','num_pages'=>7,'always_show'=>false);$posts_per_page=intval(get_query_var('posts_per_page'));$paged=absint(get_query_var('paged'));if(!$paged){$paged=1;}$total_pages=absint($wp_query->max_num_pages);if(!$total_pages){$total_pages=1;}if(1==$total_pages && !$options['always_show']){return;}$request=$wp_query->request;$numposts=$wp_query->found_posts;$pages_to_show=absint($options['num_pages']);$pages_to_show_minus_1=$pages_to_show-1;$half_page_start=floor($pages_to_show_minus_1/2);$half_page_end=ceil($pages_to_show_minus_1/2);$start_page=$paged-$half_page_start;if($start_page<=0){$start_page=1;}$end_page=$paged+$half_page_end;if(($end_page-$start_page)!=$pages_to_show_minus_1){$end_page=$start_page+$pages_to_show_minus_1;}if($end_page>$total_pages){$start_page=$total_pages-$pages_to_show_minus_1;$end_page=$total_pages;}if($start_page<=0){$start_page=1;}$out='';if($start_page>=2 && $pages_to_show<$total_pages){if(!empty($options['prev_text']))$out.=istudio_getpreviouslink($options['prev_text']);if(!empty($options['dotleft_text'])){$out.="<span class='extend'>{$options['dotleft_text']}</span>";}}foreach(range($start_page,$end_page)as $i){if($i==$paged && !empty($options['current_text'])){$current_page_text=str_replace('%PAGE_NUMBER%',number_format_i18n($i),$options['current_text']);$out.="<span class='current'>$current_page_text</span>";}else{$out.=istudio_pagenum($i,'page',$options['page_text']);}}if($end_page < $total_pages){if(!empty($options['dotright_text'])){$out.="<span class='extend'>{$options['dotright_text']}</span>";}if(!empty($options['next_text'])){$out.=istudio_getnextlink($options['next_text'],$total_pages);}$larger_page_end=0;}$out="<div class='istudio_pagenavi'>\n$out\n</div>\n";echo apply_filters('istudio_pagenavi',$out);}function istudio_getnextlink($label='Next Page &raquo;',$max_page=0){global $paged,$wp_query;if(!$max_page){$max_page=$wp_query->max_num_pages;}if(!$paged){$paged=1;}$nextpage=intval($paged)+1;if(!is_single()&&(empty($paged)|| $nextpage<=$max_page)){$attr=apply_filters('next_posts_link_attributes','');return '<a class="next" href="'.next_posts($max_page,false)."\" $attr>".preg_replace('/&([^#])(?![a-z]{1,8};)/i','&#038;$1',$label).'</a>';}}function istudio_getpreviouslink($label='&laquo; Previous Page'){global $paged;if(!is_single()&&$paged>1){$attr=apply_filters('previous_posts_link_attributes','');return '<a class="prev" href="'.previous_posts(false)."\" $attr>".preg_replace('/&([^#])(?![a-z]{1,8};)/','&#038;$1',$label).'</a>';}}function istudio_pagenum($page,$class,$raw_text,$format='%PAGE_NUMBER%'){if(empty($raw_text)){return '';}$text=str_replace($format,number_format_i18n($page),$raw_text);return "<a href='".esc_url(get_pagenum_link($page))."' class='$class'>$text</a>";}
get_template_part('functions.widget');// Custom Widget.
get_template_part('functions.custom');// Custom Functions.
?>