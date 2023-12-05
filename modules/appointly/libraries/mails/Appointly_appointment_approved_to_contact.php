<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_appointment_approved_to_contact extends App_mail_template
{
    protected $for = 'contact';

    protected $appointment;

    public $slug = 'appointment-approved-to-contact';

    /**
     * Relation ID, e.q. appointment id
     * @var mixed
     */
    public $rel_id;

    /**
     * Relation ID, e.q. appointment
     * @var mixed
     */
    public $rel_type;

    public function __construct($appointment)
    {
        parent::__construct();

        $this->appointment = $appointment;
        $this->rel_id = $appointment->id;
        $this->rel_type = 'appointment';

        // For SMS and merge fields for email
        $this->set_merge_fields('appointly_merge_fields', $this->appointment->id);
    }
    public function build()
    {
        $this->to($this->appointment->email);
    }
}
