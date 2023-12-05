<?php

defined('BASEPATH') or exit('No direct script access allowed');

include("./modules/mp3-addons/vendor/autoload.php");
include("./modules/mp3-addons/models/env.php");
// error_reporting(0);
// error_reporting(E_ALL);
// error_reporting(1);

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class Mp3_addons_model extends App_Model{
    private $bucket_name;
    private $folder_name;
    private $region;
    private $access_key;
    private $secret_key;
    
    public function __construct()
    {
        parent::__construct();
        $this->fetchCredential();
    }
    public function fetchCredential() {
        
        $desiredNames = ['bucket_name', 'folder_name', 'region', 'access_key', 'secret_key'];
        $sql = "SELECT `name`, `value` FROM `tbloptions` WHERE `name` IN ('" . implode("', '", $desiredNames) . "')";
        $query = $this->db->query($sql);
        $rows = $query->result();  
        
        foreach ($rows as $row) {
            $name = $row->name;
            $value = $row->value;
    
            switch ($name) {
                case 'bucket_name':
                    $this->bucket_name = $value;
                    break;
                case 'folder_name':
                    $this->folder_name = $value;
                    break;
                case 'region':
                    $this->region = $value;
                    break;
                case 'access_key':
                    $this->access_key = $value;
                    break;
                case 'secret_key':
                    $this->secret_key = $value;
                    break;
              
            }
        }
    }
    

    public function fetchDataFromS3AndInsertIntoDB(){

        $bucket = $this->bucket_name;
        $audiofolder = $this->folder_name;
        $region = $this->region;
        $access_key = $this->access_key;
        $secret_key = $this->secret_key;

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  =>$region,
            'credentials' => [
                'key'    => $access_key,
                'secret' => $secret_key,
            ],
        ]);

        try {

            $objects = $s3->listObjects([
                'Bucket' => $bucket,
                'Prefix' => $audiofolder,
            ]);

            if (!empty($objects['Contents'])) {

                foreach ($objects['Contents'] as $object) {
                    $result = $s3->getObject([
                        'Bucket' => $bucket,
                        'Key'    => $object['Key'],
                    ]);

                    $audioName = $object['Key'];

                    // print_r($result['Body']);
                    // exit;
                    $targetDirectory = 'modules/mp3-addons/assets/recording';

                    if (!is_dir($targetDirectory)) {
                        mkdir($targetDirectory, 0755, true);
                    }

                    $localAudioPath = 'modules/mp3-addons/assets/' . $audioName;
                    file_put_contents($localAudioPath, $result['Body']);

                }
            } else {
                echo ("No audio found in the S3 bucket.");
            }
        } catch (S3Exception $e) {
            log_message('error', 'Error fetching data from S3: ' . $e->getMessage());
        }


        $directory = 'modules/mp3-addons/assets/recording';
                    
        $fileNames = scandir($directory);

        $phoneNumberPattern = '/\d{12}-(\d{10})/';
        
        
        foreach ($fileNames as $fileName) {
            
            if (is_file($directory . '/' . $fileName) && pathinfo($fileName, PATHINFO_EXTENSION) == 'mp3') {
                if (preg_match($phoneNumberPattern, $fileName, $matches)) {

                    $phoneNumber = $matches[1];
                    
                    $stmtCheck = $this->db->query("SELECT name, id FROM tblleads WHERE phonenumber = ?", [$phoneNumber]);
                    $leadInfo = $stmtCheck->row();
                    
                    // $stmtCheck = $this->db->query("SELECT COUNT(*) FROM tblleads_information WHERE recording = ?", [$audioName]);
                    // $count = $stmtCheck->row()->count;
                    // if($count==0){
                    if ($leadInfo) {
                        $this->db->insert('tblcall_log_of_leads', [
                            'recording' => $fileName,
                            'lead_name' => $leadInfo->name,
                            'lead_id' => $leadInfo->id,

                        ]);
                    } 
                // }
                }
            }
        }
    }
    public function getAllRecords(){
        // $query = $this->db->get('tblringcentral_data');
        $query = $this->db->select("*");
            $this->db->from(db_prefix().'call_log_of_leads');
            $query=$this->db->get();
    //    echo"<pre>"; print_r($query->result());exit;

        return $query->result();
    }
}
