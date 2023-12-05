<?php

defined('BASEPATH') or exit('No direct script access allowed');

// include("./modules/mp3-addons/vendor/autoload.php");

// error_reporting(0);
// error_reporting(E_ALL);
// error_reporting(1);
use RingCentral\SDK\SDK;

include("./modules/ringcentral/vendor/autoload.php");

class Callfetchcontroller_model extends App_Model
{
    private $server_url;
    private $client_id;
    private $client_secret;
    private $jw_tocker;

    public function __construct()
    {
        parent::__construct();
        $this->fetchCredential();
    }

    public function fetchCredential()
    {
        $desiredNames = ['server_url', 'client_id', 'client_secret', 'jw_tocker'];
        $sql = "SELECT `name`, `value` FROM `tbloptions` WHERE `name` IN ('" . implode("', '", $desiredNames) . "')";
        $query = $this->db->query($sql);
        $rows = $query->result();

        foreach ($rows as $row) {
            $name = $row->name;
            $value = $row->value;

            switch ($name) {
                case 'server_url':
                    $this->server_url = $value;
                    break;
                case 'client_id':
                    $this->client_id = $value;
                    break;
                case 'client_secret':
                    $this->client_secret = $value;
                    break;
                case 'jw_tocker':
                    $this->jw_tocker = $value;
                    break;
            }
        }
    }

    public function fetchCallLog()
    {
        $SERVER_URL    = $this->server_url;
        $CLIENT_ID     = $this->client_id;
        $CLIENT_SECRET = $this->client_secret;
        $JWT_TOKEN     = $this->jw_tocker;

        $rcsdk = new SDK($CLIENT_ID, $CLIENT_SECRET, $SERVER_URL);
        $platform = $rcsdk->platform();
        $platform->login(['jwt' => $JWT_TOKEN]);

        $params = [
            'view' => 'Detailed'
        ];

        $resp = $platform->get('/account/~/call-log', $params);

        if ($resp) {
            $callLogs = $resp->json()->records;
            $pdo = new PDO('mysql:host=localhost;dbname=practice_perfex_crm', 'root', '');

            foreach ($callLogs as $callLog) {
                // Your existing code to insert call logs into the database
            }

            echo "Call logs inserted into the database successfully.";
        }
    }
}
