<?php
/**
 * @package     Gator Forms
 * @copyright   (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) exit;

$messageId = isset($messageId) && is_numeric($messageId) ? (int)$messageId : 0;

$row = isset($row) && !empty($row) ? json_decode(json_encode($row)) : null;

$rowExists = $messageId > 0 && !empty($row) && $row->id == $messageId;

if ($rowExists && isset($row->payload)) {
    $row->payload = (array)maybe_unserialize($row->payload);

    $uploadPath = wp_upload_dir();
    $formUploadsPath = sprintf('%s/pwebcontact/%d', $uploadPath['basedir'], $row->form_id);
    $formUploadsUrl = sprintf('%s/pwebcontact/%d', $uploadPath['baseurl'], $row->form_id);
    unset($uploadPath);

    $createdAt = new \DateTime($row->created_at, $utcTimeZone);
    $createdAt->setTimezone($instanceTimezone);
    $row->created_at = $createdAt->format($dateTimeFormat);
} else {
    $formUploadsPath = null;
    $formUploadsUrl = null;
}
?>

<div id="pwebcontact-p-message" class="wrap">
    <header>
        <h1>
            <?php printf(
                '%s > %s',
                _e('Gator Forms Messages', 'pwebcontact'),
                $rowExists ? '#'. $row->id : '<span style="color: #ccc;"><i>' . __('none', 'pwebcontact') . '</i></span>'
            ); ?>
        </h1>
        <nav>
            <a href="<?php echo admin_url('admin.php?page=pwebcontact-messages'); ?>" class="add-new-h2"><?php _e('Back'); ?></a>
        </nav>
    </header>
    <div>
        <?php if ($rowExists): ?>
        <div class="c-grid">
            <div class="o-grid-item o-grid__meta postbox">
                <h3><?php _e('Details', 'pwebcontact'); ?></h3>
                <div>
                    <label><?php _e('Creation Date', 'pwebcontact'); ?></label>
                    <span><?php echo date_i18n($dateTimeFormat, strtotime($row->created_at)); ?></span>
                </div>
                <div>
                    <label><?php _e('Status', 'pwebcontact'); ?></label>
                    <?php if ((bool)$row->sent): ?>
                    <span class="dashicons dashicons-yes" style="color: #2ecc71;"></span> <?php _e('Sent', 'pwebcontact'); ?>
                    <?php else: ?>
                    <span class="dashicons dashicons-no-alt" style="color: #e74c3c;"></span> <?php _e('Not sent', 'pwebcontact'); ?>
                    <?php endif; ?>
                </div>
                <div>
                    <label><?php _e('User', 'pwebcontact'); ?></label>
                    <span>
                        <?php if ((int)$row->user_id > 0): ?>
                        <a href="#" target="_blank" rel="noopener noreferer"><?php echo esc_html($row->display_name); ?></a>
                        <?php else: ?>
                        <?php _e('none', 'pwebcontact'); ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div>
                    <label for="field_ip"><?php _e('IP Address', 'pwebcontact'); ?></label>
                    <input type="text" id="field_ip" readonly value="<?php echo !empty($row->ip_address) ? esc_html($row->ip_address) : ''; ?>">
                </div>
                <div>
                    <label for="field_browser"><?php _e('Browser', 'pwebcontact'); ?></label>
                    <input type="text" id="field_browser" readonly value="<?php echo !empty($row->browser) ? esc_html($row->browser) : ''; ?>">
                </div>
                <div>
                    <label for="field_os"><?php _e('OS', 'pwebcontact'); ?></label>
                    <input type="text" id="field_os" readonly value="<?php echo !empty($row->os) ? esc_html($row->os) : ''; ?>">
                </div>
                <div>
                    <label for="ticket"><?php _e('Ticket', 'pwebcontact'); ?></label>
                    <input type="text" id="ticket" readonly value="<?php echo !empty($row->ticket) ? esc_html($row->ticket) : ''; ?>">
                </div>
            </div>
            <div class="o-grid-item o-grid__fields postbox">
                <h3><?php _e('Fields', 'pwebcontact'); ?></h3>
                <?php if (count($row->payload) > 0): ?>
                <table class="form-table">
                    <tbody>
                        <?php foreach ($row->payload as $field): ?>
                        <tr>
                            <th scope="row">
                                <label for="field_<?php echo $field->alias; ?>">
                                    <?php echo esc_html(!empty($field->label) ? $field->label : $field->alias); ?>
                                </label>
                            </th>
                            <td>
                                <?php if ($field->type === 'textarea'): ?>
                                <textarea id="field_<?php echo $field->alias; ?>" rows="4" class="large-text" readonly><?php echo esc_html($field->value); ?></textarea>
                                <?php elseif ($field->type === 'upload'): ?>
                                    <?php if (!empty($field->value)): ?>
                                        <ul>
                                        <?php
                                        foreach ($field->value as $fileName):
                                            $filePath = $formUploadsPath . '/' . $fileName;
                                            if (file_exists($filePath)): ?>
                                                <li>
                                                    <a href="<?php echo $formUploadsUrl; ?>/<?php echo $fileName; ?>" target="_blank" rel="noopener noreferer"><?php echo $fileName; ?></a>
                                                </li>
                                            <?php else:
                                                echo $fileName;
                                            endif;
                                        endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                    <p><?php _e('none', 'pwebcontact'); ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                <input type="text" id="field_<?php echo $field->alias; ?>" class="regular-text" readonly value="<?php echo esc_html(is_array($field->value) ? implode(', ', $field->value) : $field->value); ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p><?php _e('No content found.', 'pwebcontact'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <p><?php _e('Message not found.', 'pwebcontact'); ?></p>
        <?php endif; ?>
    </div>
    <footer class="c-admin-footer">
      <div class="c-rating">
        <a href="https://wordpress.org/support/plugin/pwebcontact/reviews/#new-post" target="_blank" rel="noopener noreferer">
            <?php
            printf(
                __('If you like Gator Forms please leave us a %s rating. Thank you!', 'pwebcontact'),
                '<span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>'
            );
            ?>
        </a>
      </div>
      <hr>
      <nav class="c-links">
        <a href="https://gatorforms.com" target="_blank" rel="noopener noreferer"><?php _e('Gator Forms Homepage', 'pwebcontact'); ?></a>
        <a href="https://gatorforms.com/documentation" target="_blank" rel="noopener noreferer"><?php _e('Documentation', 'pwebcontact'); ?></a>
        <a href="https://gatorforms.com/contact" target="_blank" rel="noopener noreferer"><?php _e('Contact', 'pwebcontact'); ?></a>
      </nav>
    </footer>
</div>
<?php
