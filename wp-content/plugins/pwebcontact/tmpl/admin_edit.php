<?php
/**
 * @version 2.0.14
 * @package Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;

?>

<h1 class="wp-heading-inline"><?php _e('Edit Form', 'pwebcontact'); ?></h1>
<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=pwebcontact&task=new' ), 'new-form'); ?>" class="page-title-action">
    <?php echo esc_html_x('Add New', 'link'); ?>
</a>
<hr class="wp-header-end">

<!-- header for displaying update and error messages after -->
<h2 style="display:none"></h2>
<?php $this->_display_messages(); ?>

<form name="edit" method="post" action="<?php echo esc_attr(admin_url( 'admin.php?page=pwebcontact&task=save' )); ?>" id="pweb_form">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content" style="position: relative;">

                <div id="titlediv">
                    <div id="titlewrap">
                        <input type="text" name="title" size="30" value="<?php echo esc_attr($this->data->title); ?>" id="title" placeholder="<?php esc_attr_e( 'Enter form name here', 'pwebcontact' ); ?>">
                    </div>
                </div>
                <!-- /titlediv -->

                <div id="postdivrich" class="postarea wp-editor-expand">

                    <div id="pweb-adminbar">

                        <h2 class="nav-tab-wrapper" id="pweb-tabs">
                            <a href="#pweb-tab-location" id="pweb-tab-location" class="nav-tab nav-tab-active">
                                <i class="glyphicon glyphicon-th-large"></i>
                                <?php esc_html_e( 'Location', 'pwebcontact' ); ?>
                            </a>
                            <a href="#pweb-tab-fields" id="pweb-tab-fields" class="nav-tab">
                                <i class="glyphicon glyphicon-th-list"></i>
                                <?php esc_html_e( 'Fields', 'pwebcontact' ); ?>
                            </a>
                            <a href="#pweb-tab-theme" id="pweb-tab-theme" class="nav-tab">
                                <i class="glyphicon glyphicon-picture"></i>
                                <?php esc_html_e( 'Theme', 'pwebcontact' ); ?>
                            </a>
                            <a href="#pweb-tab-email" id="pweb-tab-email" class="nav-tab">
                                <i class="glyphicon glyphicon-envelope"></i>
                                <?php esc_html_e( 'Email', 'pwebcontact' ); ?>
                            </a>
                            <a href="#pweb-tab-advanced" id="pweb-tab-advanced" class="nav-tab">
                                <i class="glyphicon glyphicon-cog"></i>
                                <?php esc_html_e( 'Advanced', 'pwebcontact' ); ?>
                            </a>
                        </h2>

                    </div>
                    <!-- /pweb-adminbar -->

                    <div id="pweb-tabs-content">

                        <div id="pweb-tab-location-content" class="nav-tab-content nav-tab-content-active pweb-clearfix">
                            <?php $this->_load_tmpl('location', __FILE__); ?>
                        </div>

                        <div id="pweb-tab-fields-content" class="nav-tab-content pweb-clearfix">
                            <?php $this->_load_tmpl('fields', __FILE__); ?>
                        </div>

                        <div id="pweb-tab-theme-content" class="nav-tab-content pweb-clearfix">
                            <?php $this->_load_tmpl('theme', __FILE__); ?>
                        </div>

                        <div id="pweb-tab-email-content" class="nav-tab-content pweb-clearfix">
                            <?php $this->_load_tmpl('email', __FILE__); ?>
                        </div>

                        <div id="pweb-tab-advanced-content" class="nav-tab-content pweb-clearfix">
                            <?php $this->_load_tmpl('advanced', __FILE__); ?>
                        </div>

                    </div>
                    <!-- /pweb-tabs-content -->

                </div>
            </div><!-- /post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">

                    <div id="submitdiv" class="postbox gf-box" style="display: block;">
                        <h2 class="hndle"><span><?php _e( 'Configuration check', 'pwebcontact' ); ?></span></h2>
                        <div class="inside">
                            <div class="submitbox" id="submitpost">

                                <div id="minor-publishing">

                                    <div class="gf-misc-actions-container">
                                        <div class="gf-misc-actions">
                                            <!-- Configuration check -->
                                            <div id="pweb-tab-check-content">
                                                <?php $this->_load_tmpl('check', __FILE__); ?>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <!-- #minor-publishing-actions -->
                                    <div class="clear"></div>
                                </div>

                                <div id="major-publishing-actions">
                                    <div id="delete-action">
                                        <a class="submitdelete deletion" href="<?php echo admin_url( 'admin.php?page=pwebcontact' ); ?>"><?php _e( 'Close' ); ?></a>
                                    </div>
                                    <div id="publishing-action">
                                        <span class="spinner"></span>
                                        <button type="submit" class="button button-primary" id="pweb-save-button">
                                            <?php _e( 'Save Form' ); ?>
                                        </button>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div id="submitdiv" class="postbox gf-box" style="display: block;">
                        <h2 class="hndle"><span><?php _e( 'Gator Forms Documentation', 'pwebcontact' ); ?></span></h2>
                        <div class="inside">
                            <div class="submitbox" id="submitpost">

                                <div id="minor-publishing">

                                    <div class="gf-misc-actions-container">
                                        <div class="gf-misc-actions">
                                            <button class="button button-primary pweb-buy" id="pweb-buy-button" data-href="<?php echo $this->buy_url; ?>" data-role="anchor">
                                                <?php _e( 'Need all the features?', 'pwebcontact' ); ?> <strong><?php _e( 'Go Pro!', 'pwebcontact' ); ?></strong>
                                            </button>
                                            <ul>
                                                <li><a href="https://gatorforms.com/documentation/create-form/" target="_blank"><?php _e( 'Creating Your Form', 'pwebcontact' ); ?></a></li>
                                                <li><a href="https://gatorforms.com/documentation/getting-started/" target="_blank"><?php _e( 'Getting started', 'pwebcontact' ); ?></a></li>
                                                <li><a href="https://gatorforms.com/documentation/emails/" target="_blank"><?php _e( 'Emails', 'pwebcontact' ); ?></a></li>
                                                <li><a href="https://gatorforms.com/documentation/layout/" target="_blank"><?php _e( 'Layout', 'pwebcontact' ); ?></a></li>
                                            </ul>
                                            <a class="button" id="pweb-docs-button" href="<?php echo $this->documentation_url; ?>" target="_blank">
                                                <?php _e( 'More documentation', 'pwebcontact' ); ?>
                                            </a>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <!-- #minor-publishing-actions -->
                                    <div class="clear"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                    <!-- A .postbox goes here -->
                </div>
            </div>
        </div><!-- /post-body -->
        <br class="clear">
    </div>

    <input type="hidden" name="id" value="<?php echo (int)$this->id; ?>">
    <?php wp_nonce_field( 'save-form_'.$this->id ); ?>

</form>