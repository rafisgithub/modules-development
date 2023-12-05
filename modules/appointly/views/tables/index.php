<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'subject',
    'description',
    'firstname as creator_firstname',
    'lastname as creator_lastname',
    'CONCAT(date, \' \', start_hour) as date',
    'finished',
    'source'
];


$sIndexColumn = 'id';
$sTable       = db_prefix() . 'appointly_appointments';

$where  = [];

if (!is_admin() && !is_staff_appointments_responsible()) {
    array_push($where, 'AND (' . db_prefix() . 'appointly_appointments.created_by=' . get_staff_user_id() . ') 
    OR ' . db_prefix() . 'appointly_appointments.id 
    IN (SELECT appointment_id FROM ' . db_prefix() . 'appointly_attendees WHERE staff_id=' . get_staff_user_id() . ')');
}

if ($this->ci->input->post('custom_view')) {

    if ($this->ci->input->post('custom_view') == 'approved') {
        $where[] = 'AND approved = "1" AND cancelled = "0"';
    }

    if ($this->ci->input->post('custom_view') == 'cancelled') {
        $where[] = 'AND cancelled= "1"';
    }

    if ($this->ci->input->post('custom_view') == 'finished') {
        $where[] = 'AND cancelled= "0" AND finished = "1" AND approved = "1"';
    }

    if ($this->ci->input->post('custom_view') == 'not_approved') {
        $where[] = 'AND approved != "1"';
    }
}

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'appointly_appointments.created_by',
];

$additionalSelect = [
    'approved',
    'created_by',
    'name',
    db_prefix() . 'appointly_appointments.email as contact_email',
    db_prefix() . 'appointly_appointments.phone',
    'cancelled',
    'contact_id',
    'google_calendar_link',
    'google_added_by_id',
    'feedback'
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {

    $label_class = 'primary';
    $tooltip = '';

    // Check with Perfex CRM default timezone configured in Setup->Settings->Localization
    if (date('Y-m-d H:i', strtotime($aRow['date'])) < date('Y-m-d H:i')) {
        $label_class = 'danger';
        $tooltip = 'data-toggle="tooltip" title="' . _l('appointment_missed') . '"';
    }

    $row = [];

    $row[] = $aRow['id'];
    $row[] = '<a href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '">' . $aRow['subject'] . '</a>';
    $row[] = '<span  ' . $tooltip . ' class="label label-' . $label_class . '">' . _dt($aRow['date']) . '</span>';

    if ($aRow['creator_firstname']) {
        $staff_fullname = $aRow['creator_firstname'] . ' ' . $aRow['creator_lastname'];

        $row[] = '<a class="initiated_by" target="_blank" href="' . admin_url() . "profile/" . $aRow["created_by"] . '"><img src="' . staff_profile_image_url($aRow["created_by"], "small") . '" data-toggle="tooltip" data-title="' . $staff_fullname . '" class="staff-profile-image-small mright5" data-original-title="" title="' . $staff_fullname . '">' . $staff_fullname . '</a>';
    } else {
        $row[] = $aRow['name'];
    }

    $row[] = $aRow['description'];

    if ($aRow['cancelled'] && $aRow['finished'] == 0) {

        $row[] = '<span class="label label-danger">' . strtoupper(_l('appointment_cancelled')) . '</span>';
    } else if (!$aRow['finished'] && !$aRow['cancelled'] && date('Y-m-d H:i', strtotime($aRow['date'])) < date('Y-m-d H:i')) {

        $row[] = '<span class="label label-danger">' . strtoupper(_l('appointment_missed_label')) . '</span>';
    } else if (!$aRow['finished'] && !$aRow['cancelled'] && $aRow['approved'] == 1) {

        $row[] = '<span class="label label-info">' . strtoupper(_l('appointment_upcoming')) . '</span>';
    } else if (!$aRow['finished'] && !$aRow['cancelled'] && $aRow['approved'] == 0) {

        $row[] = '<span class="label label-warning">' . strtoupper(_l('appointment_pending_approval')) . '</span>';
    } else {
        $row[] = '<span class="label label-success">' . strtoupper(_l('appointment_finished')) . '</span>';
    }


    if ($aRow['source'] == 'external') {
        $row[] = _l('appointments_source_external_label');
    }
    if ($aRow['source'] == 'internal') {
        $row[] = _l('appointments_source_internal_label');
    }
    if ($aRow['source'] == 'lead_related') {
        $row[] = _l('lead');
    }

    $options = '';

    // If contact id is not 0 then it means that contact is internal as for that dont show convert to lead
    $isContact = ($aRow['contact_id']) ? 0 : 1;
    $options .= '
                <div class="btn-group" data-toggle="tooltip" title="' . _l('appointments_select_option') . '">
                <button class="btn btn-primary btn-xs dropdown-toggle appointly_dropdown_options" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="fa fa-tasks"></i>
                </button>
                <ul class="dropdown-menu">
                <li><a data-toggle="tooltip" title="' . _l('appointment_view_meeting') . '" href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '">' . _l('view') . '</a></li>';

    $options .= (staff_can('create', 'tasks')) ?
        '<li><a data-toggle="tooltip" title="' . _l('appointments_create_task_tooltip') . '" href="#" data-customer-id="' . appointly_get_contact_customer_id($aRow['contact_id']) . '" data-source="' . $aRow['source'] . '" data-contact-id="' . $aRow['contact_id'] . '" data-name="' . $aRow['name'] . '" onclick="new_task_from_relation_appointment(this); return false;">' . _l('new_task') . '</a></li>'
        : '';

    $options .= ($isContact) ?
        '<li><a data-toggle="tooltip" title="' . _l('appointments_convert_to_lead_tooltip') . '" href="#" data-name="' . $aRow['name'] . '" data-email="' . $aRow['contact_email'] . '" data-phone="' . $aRow['phone'] . '" onclick="init_appointment_lead(this);return false;">' . _l("appointments_convert_to_lead_label") . '</a></li>'
        : '';

    // If there is no feedback from client and if appintment is marked as finished
    if ($aRow['feedback'] !== NULL && $aRow['finished'] !== 1) {
        $options .= '  <li><a data-toggle="tooltip" title="' . _l('appointment_view_feedback') . '" href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '#feedback_wrapper">' . _l('appointment_view_feedback') . '</a></li>';
    } else if ($aRow['finished'] == 1) {
        $options .= '<li><a onclick="request_appointment_feedback(\'' . $aRow['id'] . '\')" data-toggle="tooltip" title="' . _l('appointments_request_feedback_from_client') . '" href="#">' . _l('appointments_request_feedback') . '</a></li>';
    }

    $options .= '</ul>';
    $options .= '</div>';


    if (staff_can('edit', 'appointments') || is_staff_appointments_responsible()) {
        $options .= '<button class="btn btn-warning btn-xs mleft5" data-toggle="tooltip" title="' . _l('appointment_edit_meeting') . '" data-id="' . $aRow['id'] . '" onclick="appointmentUpdateModal(this)"><i class="fa fa-edit"></i></button>';
    }

    if (staff_can('delete', 'appointments')) {
        $options .= '<a class="btn btn-danger btn-xs mleft5" id="confirmDelete" data-toggle="tooltip" title="' . _l('appointment_dismiss_meeting') . '" href="' . admin_url('appointly/appointments/delete/' . $aRow['id']) . '" onclick="return confirm(\'' . _l('appointment_are_you_sure') . '\')"><i class="fa fa-trash"></i></a>';
    }

    if (
        $aRow['approved'] == 0
        && is_admin() && $aRow['cancelled'] == 0
        || $aRow['approved'] == 0
        && staff_can('view', 'appointments')
        && $aRow['cancelled'] == 0
    ) {
        $options .= '<a class="btn btn-info btn-xs mleft5 approve_appointment" href="' . admin_url('appointly/appointments/approve?appointment_id=' . $aRow['id']) . '">' . _l('appointment_approve') . '</a>';
    }

    if ($aRow['approved'] && $aRow['cancelled'] == 0) {
        $options .= '<p class="btn btn-success btn-xs mleft5 meeting_approved">' . _l('appointment_approved') . '</p>';
    }
    if (
        $aRow['approved']
        && $aRow['cancelled'] == 1
        || !$aRow['approved']
        && $aRow['cancelled'] == 1
        && $aRow['finished'] != 1
    ) {
        $options .= '<p class="btn btn-danger btn-xs mleft5 meeting_approved">' . _l('appointment_cancelled') . '</p>';
    }

    if (
        $aRow['google_calendar_link'] !== null
        && $aRow['google_added_by_id'] == get_staff_user_id()
    ) {
        $options .= '<a data-toggle="tooltip" title="' . _l('appointment_open_google_calendar') . '" href="' . $aRow['google_calendar_link'] . '" target="_blank" class="btn btn-primary-google btn-xs mleft5"><i class="fa fa-google" aria-hidden="true"></i></a>';
    }


    $row[] = $options;

    $output['aaData'][] = $row;
}
