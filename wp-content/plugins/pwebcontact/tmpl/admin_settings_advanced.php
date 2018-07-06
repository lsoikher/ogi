<?php
/**
 * @version 2.3.0
 * @package Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;

?>

<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'dlid',
    'group' => 'settings',
    'readonly' => true,
    'desc' => sprintf(__('Enter the license key which you can find at the %s website, to get automatic updates.<br>You have to first reinstall plugin to Pro version by yourself.', 'pwebcontact'), '<a href="https://gatorforms.com/wp-login.php" target="_blank">Gator Forms</a>'),
    'label' => 'License Key'
)); ?>


<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'force_init',
    'group' => 'settings',
    'header' => 'Advanced settings',
    'label' => 'Force to load CSS and JS at all pages',
    'tooltip' => 'Enable this option only if you are displaying contact form inside content by some AJAX plugin',
    'default' => 0,
    'class' => 'pweb-radio-group',
    'options' => array(
        array(
            'value' => 0,
            'name' => 'No'
        ),
        array(
            'value' => 1,
            'name' => 'Yes'
        )
    )
)); ?>

<?php
?>
