<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'lead_name',
    'date',
    'lead_id',
    'recording',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'call_log_of_leads';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
// print_r($result);exit;
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] === 'recording') {
            $row[] = '<audio controls style="width: 200px; height: 30px;">
                <source src="' . base_url('modules/mp3-addons/assets/recording/' . $aRow['recording']) . '" type="audio/mpeg">
            </audio>';
        } else {
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;
}
