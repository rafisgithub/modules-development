<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
      
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('uri'),
                            _l('session_id'),
                            _l('start_time'),
                            _l('duration'),
                            _l('duration_ms'),
                            _l('type'),
                            _l('internal_type'),
                            _l('direction'),
                            _l('action'),
                            _l('result'),
                            _l('to_phone_number'),
                            _l('from_name'),
                            _l('from_phone_number'),
                            _l('from_extension_id'),
                            _l('extension_uri'),
                            _l('extension_id'),
                            _l('reason'),
                            _l('reason_description'),
                            _l('telephony_session_id'),
                            _l('party_id'),
                            _l('transport'),
                            _l('last_modified_time'),
                            _l('billing_cost_included'),
                            _l('billing_cost_purchased'),
                        ], 'ringcentral'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-ringcentral', window.location.href, [5], [5]);
});
</script>
</body>
</html
