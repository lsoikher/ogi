<?php
/**
 * @package     Gator Forms
 * @copyright   (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) exit;

if (!empty($filters->startDate)) {
    $filters->startDate = date($dateFormat, strtotime($filters->startDate));
}

if (!empty($filters->endDate)) {
    $filters->endDate = date($dateFormat, strtotime($filters->endDate));
}

$getThClasses = function($columnName, $isSortable = true) use ($orderBy, $orderDir) {
    $classes = 'manage-column';

    if ($isSortable) {
        $classes = $columnName === $orderBy ? ' sorted ' . $orderDir : ' sortable asc';
    }

    return $classes;
};

$buildFilterUrl = function($columnName) use ($orderBy, $orderDir) {
    $urlParams = array(
        'page=pwebcontact-messages',
        'orderby=' . $columnName
    );

    if ($columnName === $orderBy) {
        $urlParams[] = 'orderdir=' . ($orderDir === 'desc' ? 'asc' : 'desc');
    } else {
        $urlParams[] = 'orderdir=desc';
    }

    $urlRecipe = 'admin.php?' . implode('&', $urlParams);

    $url = admin_url($urlRecipe);

    return $url;
};

$noneSpanTag = sprintf('<span style="color: #ccc;"><i>%s</i></span>', __('none', 'pwebcontact'));
?>

<div class="wrap">
    <h1><?php _e('Gator Forms Messages', 'pwebcontact'); ?></h1>
    <div>
        <div class="tablenav top">
            <form action="<?php echo admin_url('admin.php'); ?>" method="get">
                <input type="hidden" name="page" value="pwebcontact-messages">
                <div class="alignleft actions u-hidden-xs">
                    <input type="text" class="o-datepicker" id="filter_start_date" name="start_date" placeholder="<?php _e('Starting at', 'pwebcontact'); ?>" value="<?php echo $filters->startDate; ?>">
                    <input type="text" class="o-datepicker" id="filter_end_date" name="end_date" placeholder="<?php _e('Ending at', 'pwebcontact'); ?>" value="<?php echo $filters->endDate; ?>">
                </div>
                <div class="alignleft actions u-hidden-xs">
                    <select name="form" onchange="this.form.submit();">
                        <option value="" <?php echo $filters->form <= 0 ? 'selected' : ''; ?>><?php _e('Show all Forms', 'pwebcontact'); ?></option>
                        <?php foreach ($formsRowset as $form): ?>
                        <option value="<?php echo $form->id; ?>" <?php echo $filters->form === (int)$form->id ? 'selected' : ''; ?>><?php echo $form->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status" onchange="this.form.submit();">
                        <option value="" <?php echo $filters->status === -1 ? 'selected' : ''; ?>><?php _e('Show all statuses', 'pwebcontact'); ?></option>
                        <option value="1" <?php echo $filters->status === 1 ? 'selected' : ''; ?>><?php _e('Sent', 'pwebcontact'); ?></option>
                        <option value="0" <?php echo $filters->status === 0 ? 'selected' : ''; ?>><?php _e('Not sent', 'pwebcontact'); ?></option>
                    </select>
                </div>
                <p class="search-box">
                    <input type="search" name="s" value="<?php echo $filters->search; ?>">
                    <input type="submit" class="button" value="<?php _e('Search'); ?>">
                </p>
                <input type="hidden" name="orderby" value="<?php echo $orderBy; ?>">
                <input type="hidden" name="orderdir" value="<?php echo $orderDir; ?>">
            </form>
        </div>
        <div>
            <table id="the-table" class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="<?php echo $getThClasses('created_at'); ?>">
                            <a href="<?php echo $buildFilterUrl('created_at'); ?>">
                                <span><?php _e('Creation Date', 'pwebcontact'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column">
                            <?php _e('Form', 'pwebcontact'); ?>
                        </th>
                        <th scope="col" class="manage-column" style="max-width: 80px; text-align: center;">
                            <?php _e('Status', 'pwebcontact'); ?>
                        </th>
                        <th scope="col" class="<?php echo $getThClasses('ip_address'); ?>">
                            <a href="<?php echo $buildFilterUrl('ip_address'); ?>">
                                <span><?php _e('IP Adress', 'pwebcontact'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" class="<?php echo $getThClasses('browser'); ?>">
                            <a href="<?php echo $buildFilterUrl('browser'); ?>">
                                <span><?php _e('Browser', 'pwebcontact'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" class="<?php echo $getThClasses('user'); ?>">
                            <a href="<?php echo $buildFilterUrl('user'); ?>">
                                <span><?php _e('User'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rowsetCount > 0): ?>
                    <?php foreach ($rowset as $row): ?>
                    <tr>
                        <td>
                            <a class="row-title u-hidden-xs" href="<?php echo admin_url('admin.php?page=pwebcontact-messages&id=' . $row->id); ?>">
                                <?php
                                $createdAt = new \DateTime($row->created_at, $utcTimeZone);
                                $createdAt->setTimezone($instanceTimezone);
                                $row->created_at = $createdAt->format($dateTimeFormat);
                                echo $row->created_at;
                                ?>
                            </a>
                            <div class="u-visible-xs u-flex s-hor">
                                <div class="u-flex s-ver">
                                    <a class="row-title" href="<?php echo admin_url('admin.php?page=pwebcontact-messages&id=' . $row->id); ?>">
                                        <?php echo $row->created_at; ?>
                                    </a>
                                    <span><?php echo $row->title; ?></span>
                                    <span style="display: block;"><?php echo $row->ip_address; ?></span>
                                </div>
                                <span class="dashicons dashicons-<?php echo (bool)$row->sent ? 'yes" title="'. __('Sent', 'pwebcontact') .'" title="'. __('Not sent', 'pwebcontact') .'" style="color: #2ecc71;' : 'no-alt" style="color: #e74c3c;'; ?>"></span>
                            </div>
                        </td>
                        <td><?php echo $row->title; ?></td>
                        <td style="text-align: center;">
                            <span class="dashicons dashicons-<?php echo (bool)$row->sent ? 'yes" title="'. __('Sent', 'pwebcontact') .'" title="'. __('Not sent', 'pwebcontact') .'" style="color: #2ecc71;' : 'no-alt" style="color: #e74c3c;'; ?>"></span>
                        </td>
                        <td>
                            <?php echo !empty($row->ip_address) ? $row->ip_address : $noneSpanTag; ?>
                        </td>
                        <td>
                            <?php echo !empty($row->browser) ? $row->browser : $noneSpanTag; ?>
                        </td>
                        <td>
                            <?php echo (int)$row->user_id && !empty($row->display_name) ? esc_html($row->display_name) : $noneSpanTag; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <p><?php _e('No messages found.', 'pwebcontact'); ?></p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo sprintf(__('%d items', 'pwebcontact'), $totalCount); ?></span>
                    <span class="pagination-links">
                        <?php if ($pagination->currentPage > 2): ?>
                        <a href="#" data-page="1">
                            <span aria-hidden="true">«</span>
                        </a>
                        <?php else: ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                        <?php endif; ?>

                        <?php if ($pagination->currentPage > 1): ?>
                        <a class="prev-page" href="#" data-page="<?php echo $pagination->currentPage - 1; ?>">
                            <span aria-hidden="true">‹</span>
                        </a>
                        <?php else: ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                        <?php endif; ?>

                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text">
                                <?php if ($rowsetCount > 0): ?>
                                <?php echo sprintf('%d of %d', $pagination->currentPage, $pagination->pages); ?>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </span>
                        </span>

                        <?php if ($pagination->currentPage < $pagination->pages): ?>
                        <a class="prev-page" href="#" data-page="<?php echo $pagination->currentPage + 1; ?>">
                            <span aria-hidden="true">›</span>
                        </a>
                        <?php else: ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                        <?php endif; ?>

                        <?php if ($pagination->currentPage < $pagination->pages - 1): ?>
                        <a href="#" data-page="<?php echo $pagination->pages; ?>">
                            <span aria-hidden="true">»</span>
                        </a>
                        <?php else: ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
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
