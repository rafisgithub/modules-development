<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$table_name = db_prefix() . 'call_log_of_leads';

if (!$CI->db->table_exists($table_name)) {
    $CI->db->query("
        CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `recording` TEXT DEFAULT NULL,
            `lead_name` VARCHAR(20) DEFAULT NULL,
            `lead_id` INT(11) DEFAULT NULL,
            `date` DATE DEFAULT CURRENT_DATE(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_lead_id` (`lead_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
    ");

    // Modify the id column to be auto-increment
    $CI->db->query("ALTER TABLE `$table_name` MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT");
}
?>