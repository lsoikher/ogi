<?php
/**
 * @version 2.0.0
 * @package Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;
?>


<div id="pweb-cog-check-success" class="pweb-alert pweb-alert-success" style="display:none">
    <?php _e('Congratulations your form is ready!', 'pwebcontact'); ?>
</div>

<div id="pweb-cog-check-warning" class="pweb-alert pweb-alert-success" style="display:none">
    <?php _e('Congratulations your form is ready! However, you have chosen some PRO options, so please buy Gator Forms Pro.', 'pwebcontact');
        //TODO check email template for: ip_address, browser, os, screen_resolution, mailto_name, ticket
    ?>
</div>

<div id="pweb-cog-check-error" class="pweb-alert pweb-alert-danger" style="display:none">
    <?php _e('There are still some options required to get your form working.', 'pwebcontact'); ?>
</div>

<div id="pweb-cog-check">

    <div class="pweb-alert pweb-alert-danger" id="pweb-email-to-warning" style="display:none">
        <?php _e('Please go to the Email tab and enter an email address to receive messages.', 'pwebcontact'); ?>
    </div>

    <?php if (($result = $this->_check_mailer()) !== true) : ?>
    <div class="pweb-alert pweb-alert-danger">
        <?php echo $result; ?>
    </div>
    <?php endif; ?>


    <?php
    //TODO check if copy to user field is allowed
    //TODO warn about shortcode and widget position
    //TODO warn about browser detection if used in email template and 3-rd part plugin not installed
    ?>


    <?php if (($result = $this->_check_cache_path()) !== true) : ?>
    <div class="pweb-alert pweb-alert-danger">
        <?php echo $result; ?>
    </div>
    <?php endif; ?>


    <?php if (($result = $this->_check_image_text_creation()) !== true) : ?>
    <div class="pweb-alert pweb-alert-warning">
        <?php echo $result; ?>
    </div>
    <?php endif; ?>

</div>
