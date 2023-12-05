<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mp3_addons extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mp3_addons_model');
    }
    public function index(){
       
        $this->Mp3_addons_model->fetchCredential();
        $this->Mp3_addons_model->fetchDataFromS3AndInsertIntoDB();

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('mp3-addons', 'table'));
        }
        

        $allCallDetails = $this->Mp3_addons_model->getAllRecords();
    //    echo('<pre>'); print_r($allCallDetails);exit;
        $this->load->view('manage', ['allCallDetails' => $allCallDetails]);
    }
}

