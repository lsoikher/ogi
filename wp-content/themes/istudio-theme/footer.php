<footer>
	<p><?php printf('&copy;2012 <a href="'.home_url('/').'">'.get_bloginfo('name').'</a>'.__(' &#183; ').'<a href="'.esc_url('http://xuui.net/wordpress/istudio-theme-release.html').'">'.themename.'</a>'.__(' &#183; ').'Designed by <a href="'.esc_attr('http://xuui.net/').'">Xu.hel</a> in ChengDu.');printf(__(' &#183; Powered by <a href="http://wordpress.org/">WordPress</a>'));?></p>
		<!--<?php echo get_num_queries();?> queries. <?php timer_stop(1);?> seconds. -->
</footer>
</div>
<?php wp_footer();if(is_home()&&istOption('GrowlSwitch')&&istOption('GrowlInfo')){$GrowlInfos=istOption('GrowlInfo');?>
<script type="text/javascript">
<!--
var istoup_info='<?php echo $GrowlInfos;?>';istojGrowlt(istoup_info,8000);
-->
</script>
<?php }?>
</body>
</html>