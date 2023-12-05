<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance(); 

if (!$CI->db->table_exists(db_prefix() . 'ringcentral_data')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ringcentral_data` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uri` varchar(255) DEFAULT NULL,
        `sessionId` varchar(255) DEFAULT NULL,
        `startTime` timestamp NULL DEFAULT NULL,
        `duration` int(11) DEFAULT NULL,
        `durationMs` int(11) DEFAULT NULL,
        `type` varchar(255) DEFAULT NULL,
        `internalType` varchar(255) DEFAULT NULL,
        `direction` varchar(255) DEFAULT NULL,
        `action` varchar(255) DEFAULT NULL,
        `result` varchar(255) DEFAULT NULL,
        `toPhoneNumber` varchar(255) DEFAULT NULL,
        `fromName` varchar(255) DEFAULT NULL,
        `fromPhoneNumber` varchar(255) DEFAULT NULL,
        `fromExtensionId` varchar(255) DEFAULT NULL,
        `extensionUri` varchar(255) DEFAULT NULL,
        `extensionId` varchar(255) DEFAULT NULL,
        `reason` varchar(255) DEFAULT NULL,
        `reasonDescription` text DEFAULT NULL,
        `telephonySessionId` varchar(255) DEFAULT NULL,
        `partyId` varchar(255) DEFAULT NULL,
        `transport` varchar(255) DEFAULT NULL,
        `lastModifiedTime` timestamp NULL DEFAULT NULL,
        `billingCostIncluded` int(11) DEFAULT NULL,
        `billingCostPurchased` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ringcentral_data`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');
}
