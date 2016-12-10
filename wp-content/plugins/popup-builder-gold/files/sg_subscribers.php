<?php
require_once(SG_APP_POPUP_CLASSES.'/sgDataTable/SGSubscribers.php');

$haseList = SgPopupPro::isEmptySubsriptionForms();
?>
<div class="wrap subscribers-wrapper">
	<div class="headers-wrapper">
	<h2>Subscriber list
		<a href="<?php echo admin_url();?>admin-post.php?action=csv_file" class="add-new-h2">Get list</a>
		<?php if($haseList): ?>
			<a href="#addNewSubscriber" class="add-new-h2 add-subsriber">Add user to list</a>
		<?php endif; ?>
	</h2>
		<?php if(POPUP_BUILDER_PKG == POPUP_BUILDER_PKG_FREE): ?>
			<input type="button" class="main-update-to-pro" value="Upgrade to PRO version" onclick="window.open('<?php echo SG_POPUP_PRO_URL;?>')">
		<?php endif; ?>
	</div>
	<div class="subs-delete-button-wrapper">
		<img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../../../'); ?>" alt="gif" class="spiner-subscribers js-sg-spinner sg-hide-element js-sg-import-gif">
		<input type="button" value="Delete subsriber(s)" class="sg-subs-delete-button button-primary">
	</div>
	<?php
		$table = new SGPB_SubscribersView();
		echo $table;
		SGFunctions::showInfo();
	?>
</div>
<div id="addNewSubscriber">
	<div class="addNewSubsribersContent">
		<h1>Add To List</h1>
		<hr>
		<span class="liquid-width sg-admin-popup-labeles">Subscription types</span><br>
		<?php
		$allData = SgPopupGetData::getAllSubscriptionForms();
		$fistElement = array_values($allData);
		$fistElement = @$fistElement[0];
		echo SGFunctions::createSelectBox($allData, $fistElement, array('class'=>'js-sg-newslatter-forms sg-admin-popup-inputs','multiple'=>'multiple','size'=>5));
		?><br>
		<span class="liquid-width sg-admin-popup-labeles">First name</span><br>
		<input type="text" name="subs-firstName" class="subs-first-name sg-admin-popup-inputs"><br>
		<span class="liquid-width sg-admin-popup-labeles">Last name</span><br>
		<input type="text" name="subs-firstName" class="subs-last-name sg-admin-popup-inputs">
		<br><span class="liquid-width sg-admin-popup-labeles">Email</span><br>
		<input type="text" name="subs-email" class="add-subs-email sg-admin-popup-inputs"><br>
		<div class="sg-hide-element sg-email-error">Please enter valid Email.</div>
		<hr class="sg-bottom-hr">
		<input type="button" value="Add to list" class="button-primary sg-add-to-list-button">
		<img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../../../'); ?>" alt="gif" class="spiner-subscribers js-sg-spinner sg-hide-element js-sg-import-gif">
		<div class="sg-successfully sg-hide-element">Successfully added</div>
		<a href="#close" title="Close" class="sg-close">X</a>

	</div>
</div>
