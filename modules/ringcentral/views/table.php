<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'uri',
    'sessionId',
    'startTime',
    'duration',
    'durationMs',
    'type',
    'internalType',
    'direction',
    'action',
    'result',
    'toPhoneNumber',
    'fromName',
    'fromPhoneNumber',
    'fromExtensionId',
    'extensionUri',
    'extensionId',
    'reason',
    'reasonDescription',
    'telephonySessionId',
    'partyId',
    'transport',
    'lastModifiedTime',
    'billingCostIncluded',
    'billingCostPurchased',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'ringcentral_data';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
