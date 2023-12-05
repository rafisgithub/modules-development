<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
$appointly_default_table_filter = get_meta('staff', get_staff_user_id(), 'appointly_default_table_filter');
$appointly_show_summary = get_meta('staff', get_staff_user_id(), 'appointly_show_summary');
?>

<div id="wrapper">
    <div class="content">
        <?php if (get_option('appointly_responsible_person') == '') { ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <?= _l('appointments_resp_person_not_set'); ?> <a href="<?= admin_url('settings?group=appointly-settings'); ?>"><?= _l('appointly_settings_label_pointer'); ?></a> <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php } ?>
        <?php if (get_option('callbacks_responsible_person') == '') { ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <?= _l('callbacks_resp_person_not_set'); ?> <a href="<?= admin_url('settings?group=appointly-settings'); ?>"><?= _l('appointly_settings_label_pointer'); ?></a> <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php } ?>
        <?php if (isset($td_appointments) && !empty($td_appointments)) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <span class="label label-info label-big pull-right mtop5"><?= _d(date('Y-m-d')); ?></span>
                            <h4><?= _l('appointment_todays_appointments'); ?></h4>
                            <hr class="mbot0">
                            <?php foreach ($td_appointments as $appointment) : ?>
                                <div class="todays_appointment col-2 mleft20 appointly-secondary pull-left mtop10">
                                    <h3 class="text-muted mtop1"><a href="<?= admin_url('appointly/appointments/view?appointment_id=' . $appointment['id']); ?>"><?= $appointment['subject']; ?></a></h3>
                                    <span class="text-muted span_limited">
                                        <?= _l('appointment_description'); ?> <?= $appointment['description']; ?>
                                    </span>
                                    <h5 class="no-margin">
                                        <span class="text-warning"><?= _l('appointment_scheduled_at'); ?> </span>
                                        <?= date("H:i A", strtotime($appointment['start_hour'])); ?>
                                    </h5>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <span class="label label-info label-big pull-right mtop5"><?= _d(date('Y-m-d')); ?></span>
                            <h4><?= _l('appointment_no_appointments'); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if ($appointly_show_summary == 1) : ?>
                            <?php include_appointment_view('tables', 'summary'); ?>
                        <?php endif; ?>

                        <div class="_buttons">
                            <?php if (staff_can('create', 'appointments') || is_staff_appointments_responsible()) { ?>
                                <button type="button" id="createNewAppointment" class="btn btn-info pull-left display-block">
                                    <?php echo _l('appointment_new_appointment'); ?>
                                </button>
                            <?php } else { ?>
                                <button type="button" disabled class="btn btn-info pull-left display-block">
                                    <?php echo _l('appointment_new_appointment'); ?> </button>
                            <?php } ?>
                            <a href="<?= admin_url('appointly/callbacks'); ?>" id="backToAppointments" class="btn btn-info pull-left display-block mleft10">
                                <?php echo 'Callbacks'; ?>
                            </a>
                            <?php if (get_option('google_client_id') !== '' && get_option('appointly_google_client_secret') !== '') { ?>
                                <?php if (!appointlyGoogleAuth()) : ?>
                                    <a href="<?= site_url('appointly/google/auth/login'); ?>" class="btn btn-info pull-right mleft10"><?= _l('appointments_sign_in_google'); ?> <i class="fa fa-google" aria-hidden="true"></i></a>
                                <?php else : ?>
                                    <a data-toggle="tooltip" title="<?= _l('appointments_google_revoke') ?>" href="<?= site_url('appointly/google/auth/logout'); ?>" class="btn label-warning pull-right label-big mleft10"><?= _l('appointments_sign_out_google'); ?> <i class="fa fa-google" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            <?php } ?>
                            <div class="_filters _hidden_inputs hidden">
                                <?php echo form_hidden(
                                    'custom_view',
                                    get_meta('staff', get_staff_user_id(), 'appointly_default_table_filter')
                                        ? get_meta('staff', get_staff_user_id(), 'appointly_default_table_filter')
                                        : 'approved'
                                ); ?>
                            </div>
                            <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left width300 height500">
                                    <li class="filter-group <?= ($appointly_default_table_filter == 'all') ? 'active' : ''; ?>">
                                        <a href="#" data-cview="all" onclick="dt_custom_view('','.table-appointments',''); return false;">
                                            <?php echo _l('all'); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li class="filter-group
                                         <?=
                                                ($appointly_default_table_filter == 'approved'
                                                    || $appointly_default_table_filter == '')
                                                    ? 'active'
                                                    : '';
                                            ?> " data-filter-group="approved">
                                        <a href="#" data-cview="approved" onclick="dt_custom_view('approved','.table-appointments'); return false;">
                                            <?= _l('appointment_approved'); ?>
                                        </a>
                                    </li>
                                    <li class="filter-group  <?= ($appointly_default_table_filter == 'not_approved') ? 'active' : ''; ?>">
                                        <a href="#" data-cview="not_approved" onclick="dt_custom_view('not_approved','.table-appointments'); return false;">
                                            <?= _l('appointment_not_approved'); ?>
                                        </a>
                                    </li>
                                    <li class="filter-group  <?= ($appointly_default_table_filter == 'cancelled') ? 'active' : ''; ?>">
                                        <a href="#" data-cview="cancelled" onclick="dt_custom_view('cancelled','.table-appointments'); return false;">
                                            <?= _l('appointment_cancelled'); ?>
                                        </a>
                                    </li>
                                    <li class="filter-group  <?= ($appointly_default_table_filter == 'finished') ? 'active' : ''; ?>">
                                        <a href="#" data-cview="finished" onclick="dt_custom_view('finished','.table-appointments'); return false;">
                                            <?= _l('appointment_finished'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable(array(
                            _l('id'),
                            _l('appointment_subject'),
                            _l('appointment_meeting_date'),
                            _l('appointment_initiated_by'),
                            _l('appointment_description'),
                            _l('appointment_status'),
                            _l('appointment_source'),
                            _l('options'),
                        ), 'appointments'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<?php require('modules/appointly/assets/js/index_main_js.php'); ?>
</body>

</html>