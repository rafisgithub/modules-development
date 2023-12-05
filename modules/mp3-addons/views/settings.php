<?php 
defined('BASEPATH') or exit('No direct script access allowed');
 ?>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="general">
        <?php if (is_admin()) { ?>
            <div class="form-group">
                <label for="bucket_name"><?= _l('bucket_name'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('bucket_name'); ?>" id="bucket_name" name="settings[bucket_name]">
            </div>
            <div class="form-group">
                <label for="folder_name"><?= _l('folder_name'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('folder_name'); ?>" id="folder_name" name="settings[folder_name]">
            </div>
            <div class="form-group">
                <label for="region"><?= _l('region_name'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('region'); ?>" id="region" name="settings[region]">
            </div>
            <div class="form-group">
                <label for="access_key"><?= _l('access_key'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('access_key'); ?>" id="access_key" name="settings[access_key]">
            </div>
            <div class="form-group">
                <label for="secret_key"><?= _l('secret_key'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('secret_key'); ?>" id="secret_key" name="settings[secret_key]">
            </div>
            <hr />
        <?php } ?>

        
    </div>
</div>
