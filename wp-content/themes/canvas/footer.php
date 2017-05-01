<?php

/**

 * Footer Template

 *

 * Here we setup all logic and XHTML that is required for the footer section of all screens.

 *

 * @package WooFramework

 * @subpackage Template

 */



 global $woo_options;



 woo_footer_top();

 	woo_footer_before();

?>

	<footer id="footer" class="col-full">



		<?php woo_footer_inside(); ?>



		<div id="copyright" class="col-left">

			<?php woo_footer_left(); ?>

		</div>



		<div id="credit" class="col-right">

			<?php woo_footer_right(); ?>

		</div>



	</footer>



	<?php woo_footer_after(); ?>



	</div><!-- /#inner-wrapper -->



</div><!-- /#wrapper -->



<div class="fix"></div><!--/.fix-->



<?php wp_footer(); ?>

<?php woo_foot(); ?>

</body>
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//control.mockingfish.com/js/01210.js?" + Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>
</html>