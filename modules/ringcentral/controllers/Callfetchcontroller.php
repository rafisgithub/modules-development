<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Callfetchcontroller extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Callfetchcontroller_model');
    }
    public function fetchcall(){
       
        $this->Callfetchcontroller_model->fetchCredential();
        $this->Callfetchcontroller_model->fetchCallLog();
        
    }
}

