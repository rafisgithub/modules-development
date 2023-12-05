<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content no-padding">
        <div class="panel_s">
            <div class="panel-body">
                <div class="col-md-12">
                    <?php if (staff_can('view', 'appointments') || staff_can('view_own', 'appointments') || is_staff_appointments_responsible()) : ?>
                        <a href="<?= admin_url('appointly/appointments'); ?>" class="btn btn-default appointment_go_back">
                            <?= _l('go_back'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="col-md-12 mtop15">
                    <div class="panel-heading info-header">
                        <h3 class="pull-left"> <?= _l('appointment_overview'); ?></h3>
                        <a data-toggle="tooltip" title="<?= _l('appointment_public_url'); ?>" class="appointment_public_url pull-right" href="<?= $appointment['public_url']; ?>" target="_blank">
                            <i class="fa fa-external-link-square appointment_public_link" aria-hidden="true"></i>
                        </a>
                    </div>
                    <span class="spmodified_fit_center">
                        <boldit><?= _l('appointment_status_text'); ?></boldit>
                        <h3 class="appointment_status_info">
                            <?php
                            if ($appointment['cancelled']) {
                                echo '<span class="label label-danger">' . strtoupper(_l('appointment_cancelled')) . '</span>';
                            } else if (
                                !$appointment['finished']
                                && !$appointment['cancelled']
                                && !$appointment['approved']
                                && date('Y-m-d H:i', strtotime($appointment['date'] . ' ' . $appointment['start_hour'])) < date('Y-m-d H:i')
                            ) {
                                echo '<span class="label label-danger">' . strtoupper(_l('appointment_missed_label')) . '</span>';
                            } else if (
                                !$appointment['finished']
                                && !$appointment['cancelled']
                                && $appointment['approved'] == 1
                            ) {
                                echo '<span class="label label-info">' . strtoupper(_l('appointment_upcoming')) . '</span>';
                            } else if (
                                !$appointment['finished']
                                && !$appointment['cancelled']
                                && $appointment['approved'] == 0
                            ) {
                                echo '<span class="label label-warning">' . strtoupper(_l('appointment_pending_approval')) . '</span>';
                                if (
                                    $appointment['approved'] == 0
                                    && $appointment['cancelled'] == 0
                                    && is_admin() || $appointment['approved'] == 0
                                    && $appointment['cancelled'] == 0
                                    && staff_can('view', 'appointments')
                                ) {
                                    echo '<a class="label label-info mleft5 approve_appointment_single" onClick="disableButtonsAfterDelete()" href="' . admin_url('appointly/appointments/approve?appointment_id=' . $appointment['id']) . '">' . _l('appointment_approve') . '</a>';
                                }
                            } else {
                                echo '<span class="label label-success">' . strtoupper(_l('appointment_finished')) . '</span>';
                            }
                            ?>
                        </h3>
                    </span>
                </div>
                <div class="row text-center">

                    <?php if ($appointment['cancel_notes'] !== NULL && $appointment['finished'] != 1) : ?>
                        <div>
                            <?php if ($appointment['cancelled'] == 0) : ?>
                                <span class="label label-danger label-big mbot20"><a class="text-white" href="#cancelAppointment"><?= _l('appointment_request_cancellation'); ?></a></span><br>
                            <?php endif; ?>

                            <label class="label label-warning label-big label_canceL_notes_parent">
                                <strong style="color:#333;font-weight:700;line-height:20px"><?= _l('appointment_cancellation_description_label'); ?>:</strong>
                                <span style="line-height:20px" class="meeting_cancel_notes_client"><?= $appointment['cancel_notes']; ?></span>
                            </label>

                        <?php endif; ?>

                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <h4 class="appointly-default reorder-content"><?= _l('appointment_general_info'); ?></h4>
                            <span class="spmodified">

                                <boldit><?= _l('appointment_initiated_by'); ?></boldit>
                                <?=
                                    ($appointment['created_by'])
                                        ? get_staff_full_name($appointment['created_by'])
                                        : $appointment['name'];
                                ?>
                            </span><br>

                            <span class="spmodified">
                                <boldit><?= _l('appointment_subject'); ?></boldit>
                                <?= $appointment['subject']; ?>
                            </span><br>

                            <span class="spmodified">
                                <boldit><?= _l('appointment_description'); ?></boldit>
                                <?= $appointment['description']; ?>
                            </span><br>

                            <span class="spmodified">
                                <boldit><?= _l('appointment_meeting_time'); ?></boldit>
                                <?= _d($appointment['date']); ?>
                            </span><br>

                            <span class="spmodified">
                                <boldit><?= _l('appointment_squeduled_at_text'); ?></boldit>
                                <?= date("H:i A", strtotime($appointment['start_hour'])); ?>
                            </span><br>

                            <div class="spmodified attendees">
                                <boldit><?= _l('appointment_staff_attendees'); ?></boldit>

                                <?php if (!empty($appointment['attendees'])) {

                                    foreach ($appointment['attendees'] as $staff) : ?>

                                        <a target="_blank" href="<?= admin_url() . 'profile/' . $staff['staffid'];  ?>">
                                            <img src="<?= staff_profile_image_url($staff['staffid'], 'small'); ?>" data-toggle="tooltip" data-title="<?= $staff['firstname'] . ' ' . $staff['lastname']; ?>" class="staff-profile-image-small mright5" data-original-title="" title="<?= $staff['firstname'] . ' ' . $staff['lastname'] ?>">
                                        </a>

                                    <?php endforeach; ?>

                                <?php } else { ?>
                                    <strong> - &nbsp; <?= _l('appointment_no_assigned_staff_found'); ?></strong>
                                <?php } ?>

                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <h4 class="appointly-default reorder-content"><?= _l('appointment_additional_info'); ?></h4>
                            <div class="appointly_single_container">

                                <span class="spmodified">
                                    <boldit><?= _l('appointment_source'); ?></boldit>
                                    <?php
                                    if ($appointment['source'] == 'lead_related') {
                                        echo  _l('appointment_source_leads_label');
                                    }
                                    if ($appointment['source'] == 'internal') {
                                        echo _l('appointment_source_internal');
                                    }
                                    if ($appointment['source'] == 'external') {
                                        echo _l('appointment_source_external_text');
                                    }
                                    ?>
                                </span><br>

                                <span class="spmodified">
                                    <boldit><?= _l('appointment_name'); ?></boldit><?= isset($appointment['name']) ? $appointment['name'] : $appointment['details']['full_name']; ?>
                                </span><br>

                                <span class="spmodified">
                                    <?php $mail_to = isset($appointment['email']) ? $appointment['email'] : $appointment['details']['email']; ?>
                                    <boldit><?= _l('appointment_email'); ?></boldit><a href="mailto:<?= $mail_to; ?>"><?= $mail_to; ?></a>
                                </span><br>

                                <span class="spmodified client_phone">
                                    <boldit><?= _l('appointment_phone'); ?></boldit>
                                    <?php
                                    $phoneToCall = isset($appointment['details']['phone'])
                                        ? ($appointment['details']['phone'])
                                        : ($appointment['phone'])
                                        ? $appointment['phone']
                                        : '';
                                    ?>
                                    <?php if ($phoneToCall !== '') : ?>
                                        <div class="client_numbers">
                                            <a data-toggle="tooltip" class="label label-success" title="<?= _l('appointment_send_an_sms'); ?>" href="sms:<?= $phoneToCall; ?>&body=Hello">SMS: <?= $phoneToCall; ?></a>
                                            <a data-toggle="tooltip" class="label label-success mleft5" title="<?= _l('appointment_call_number') ?>" href="tel:<?= $phoneToCall; ?>">Call: <?= $phoneToCall; ?></a>
                                        </div>
                                    <?php endif; ?>
                                </span><br>
                                <span class="spmodified">
                                    <boldit><?= _l('appointment_location_address'); ?></boldit>
                                    <?php $appAddress =  $appointment['address'] ? $appointment['address'] : ''; ?>

                                    <a data-toggle="tooltip" title="Open in Google Maps" target="_blank" href="https://maps.google.com/?q=<?= $appAddress; ?>"><?= $appAddress; ?></a>
                                </span><br>
                                <?php if ($appointment['type_id'] != 0) { ?>
                                    <span class="spmodified">
                                        <boldit><?= _l('appointments_type_heading'); ?></boldit>
                                        <?= get_appointment_type($appointment['type_id']); ?>
                                    </span><br>
                                <?php } ?>
                                <span class="spmodified">
                                    <boldit><?= _l('appointment_email_tracking'); ?></boldit>
                                    <?php $isEmailRead = get_tracked_emails($appointment['id'], 'appointment');

                                    if (!empty($isEmailRead) && $isEmailRead[0]['opened']) { ?>
                                        <span data-toggle="tooltip" title="<?= _l('appointment_email_read_at'); ?>">
                                            <?= _l('appointment_email_read_at') . ' <strong>' . $isEmailRead[0]['date'] . '</strong>'; ?>
                                        </span>
                                    <?php } else { ?>
                                        <span data-toggle="tooltip" title="<?= _l('appointment_email_not_read'); ?>">
                                            <?= _l('appointment_email_not_read'); ?>
                                        </span>
                                    <?php }  ?>
                                </span><br>
                            </div>
                        </div>
                        <?php
                        $custom_fields = get_custom_fields('appointly');
                        $allFieldsEmpty = [];
                        if ($custom_fields) {
                            foreach ($custom_fields as $f) {
                                $value = get_custom_field_value($appointment['id'], $f['id'], 'appointly');
                                if ($value != '') {
                                    $allFieldsEmpty[] = $value;
                                }
                            }
                            if (!empty($allFieldsEmpty)) {
                                echo '<div class="text-center"><h6 class="label label-big label-default">' . _l('custom_fields') . '</h6></div>';
                                echo '<div class="col-lg-12 col-xs-12" id="cf_appointly">';
                            }
                            foreach ($custom_fields as $field) {
                                $value = get_custom_field_value($appointment['id'], $field['id'], 'appointly'); ?>
                                <?php if ($value != '') { ?>
                                    <span class="spmodified col-md-12 mtop15">
                                        <boldit><?= $field['name'] ?></boldit>
                                        <strong>
                                            <?= ($value != '' ? $value : '-'); ?>
                                        </strong>
                                    </span><br>
                                <?php } ?>
                            <?php }
                            if (!empty($allFieldsEmpty)) { ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php
        if (isset($appointment['notes']) && trim($appointment['notes']) !== '') { ?>
            <div class="col-lg-12 col-xs-12 mtop20">
                <h4 class="appointly-default reorder-content"><?= _l('appointment_client_notes'); ?></h4>
                <div class="appointly_single_container">
                    <div class="spmodified appointment_notes">
                        <?= $appointment['notes']; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (
            $appointment['reminder_before_type'] !== NULL
            && $appointment['reminder_before'] !== NULL
            && $appointment['approved'] == 1
        ) { ?>
            <div class="col-lg-12 col-xs-12">
                <h4 class="appointly-default reorder-content"><?= _l('appointment_notified'); ?></h4>
                <div class="appointly_single_container">
                    <span class="spmodified">
                        <boldit><?= _l('appointment_notified_by_sms'); ?>&nbsp;<small><?= _l('appointments_applies_for_clients'); ?></small></boldit>
                        <?= ($appointment['notification_date'] !== NULL && $appointment['by_sms']) ? _l('appointment_yes') : _l('appointment_no'); ?>
                    </span><br>
                    <span class="spmodified">
                        <boldit><?= _l('appointment_notified_by_email'); ?></boldit>
                        <?= ($appointment['notification_date'] !== NULL && $appointment['by_email']) ? _l('appointment_yes') : _l('appointment_no'); ?>
                    </span> <br>
                    <?php if ($appointment['notification_date'] !== NULL) : ?>
                        <span class="spmodified mbot25">
                            <boldit class="mtop10"><?= _l('appointment_notified_at'); ?> </boldit>
                            <h4 class="label label-info label-big font-medium"><?= _dt($appointment['notification_date']); ?></h4>
                            </span<br>
                        <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-12 col-xs-12">
                <div class="pull-left early_reminders_parent mtop25">
                    <?php if (
                        $appointment['reminder_before_type'] !== NULL
                        && $appointment['reminder_before'] !== NULL
                        && $appointment['approved'] == 1
                    ) { ?>
                        <button data-toggle="tooltip" title="<?= _l('appointment_manually_send_reminders_info'); ?>" class="btn btn-primary btn-xs mbot10" type="submit" onClick="sendAppointmentReminders()" id="sendAppointmentReminders"><?= _l('appointment_send_early_reminders_label'); ?></button>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="pull-right appointment_group_buttons mtop25">

                <?php if (staff_can('edit', 'appointments') || is_staff_appointments_responsible()) { ?>

                    <?php if ($appointment['cancelled'] == 0 && $appointment['finished'] == 0) { ?>
                        <button class="btn btn-danger" type="submit" onClick="cancelAppointment()" id="cancelAppointment"><?= _l('appointment_cancel'); ?></button>

                    <?php } ?>

                    <?php if ($appointment['finished'] == 0 && $appointment['cancelled'] == 0) { ?>
                        <button class="btn btn-primary" type="submit" onClick="markAppointmentAsFinished()" id="markAsFinished"><?= _l('appointment_mark_as_finished'); ?></button>

                    <?php } ?>

                    <?php if ($appointment['cancelled'] == 1 && $appointment['finished'] == 0) { ?>
                        <button class="btn btn-primary" type="submit" onClick="markAppointmentAsOngoing()" id="markAppointmentAsOngoing"><?= _l('appointment_mark_as_ongoing'); ?></button>

                    <?php } ?>

                <?php } ?>

                <?php if (staff_can('delete', 'appointments') || is_staff_appointments_responsible()) { ?>

                    <a class="btn btn-danger" id="confirmDelete" data-toggle="tooltip" title="<?= _l('appointment_dismiss_meeting');  ?>" href="<?= admin_url('appointly/appointments/delete/' . $this->input->get('appointment_id'));  ?>" onclick="if(confirm('<?= _l('appointment_are_you_sure'); ?>')){ disableButtonsAfterDelete(); }"><?= _l('delete'); ?></a>

                <?php }

                if ($appointment['google_calendar_link'] !== null && $appointment['google_added_by_id'] == get_staff_user_id()) { ?>

                    <a data-toggle="tooltip" title="<?= _l('appointment_open_google_calendar'); ?>" href="<?= $appointment['google_calendar_link']; ?>" target="_blank" class="btn btn-primary-google"><i class="fa fa-google" aria-hidden="true"></i></a>

                <?php } ?>
            </div> <!-- end - appointment_group_buttons -->
            <?php
            if ($appointment['finished'] == 1 && $appointment['feedback'] !== NULL) {
                echo renderAppointmentFeedbacks($appointment);
            }
            ?>
            </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="modal_wrapper"></div>
</body>
<?php init_tail(); ?>
<?php require('modules/appointly/assets/js/tables_appointment_js.php'); ?>

</html>