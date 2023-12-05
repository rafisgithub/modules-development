<?php 
defined('BASEPATH') or exit('No direct script access allowed');
 ?>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="general">
        <?php if (is_admin()) { ?>
            <div class="form-group">
                <label for="server_url"><?= _l('server_url'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('server_url'); ?>" id="server_url" name="settings[server_url]">
            </div>
            <div class="form-group">
                <label for="client_id"><?= _l('client_id'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('client_id'); ?>" id="client_id" name="settings[client_id]">
            </div>
            <div class="form-group">
                <label for="client_secret"><?= _l('client_secret'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('client_secret'); ?>" id="client_secret" name="settings[client_secret]">
            </div>
            <div class="form-group">
                <label for="jw_tocken"><?= _l('jw_tocken'); ?></label>
                <input type="text" class="form-control" value="<?= get_option('jw_tocken'); ?>" id="jw_tocken" name="settings[jw_tocken]">
            </div>
           
            <hr />
        <?php } ?>

        
    </div>
</div>
