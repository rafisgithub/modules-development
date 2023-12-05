<?php

// defined('BASEPATH') or exit('No direct script access allowed');

// class Ringcentral extends AdminController
// {
    // public function index(){
        
        
        // $this->load->model('Ringcentral_model');
        // if ($this->input->is_ajax_request()) {
            // $this->app->get_table_data(module_views_path('ringcentral', 'table'));
        // }
        // $allringcentralRecords = $this->Ringcentral_model->getAllRecords();
        // echo("<pre>");print_r($allringcentralRecords);exit;

        // $this->load->view('manage', ['allringcentralRecords' => $allringcentralRecords]);
    // }
// }


defined('BASEPATH') or exit('No direct script access allowed');



class Ringcentral extends AdminController
{
    public function index()
    {
        $this->load->model('Ringcentral_model');

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('ringcentral', 'table'));
        }
        

        $allringcentralRecords = $this->Ringcentral_model->getAllRecords();
        
        $this->load->view('manage', ['allringcentralRecords' => $allringcentralRecords]);
      
        
    }
}

