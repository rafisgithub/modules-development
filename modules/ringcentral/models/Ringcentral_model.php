<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ringcentral_model extends App_Model
{
    public function __construct(){
        parent::__construct();
    }
    public function getAllRecords(){
        // $query = $this->db->get('tblringcentral_data');
        $query = $this->db->select("*");
            $this->db->from(db_prefix().'ringcentral_data');
            $query=$this->db->get();
    //    echo"<pre>"; print_r($query->result());exit;

        return $query->result();
    }
    
}

