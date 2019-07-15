<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'APP_Controller.php';
class Login extends APP_Controller {

  public function __construct() {
    parent:: __construct();
    $this->load->database();
  }


    public function loginUser()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if(isset($data['username']) && isset($data['password']))
        {            
            $result = $this->db->select('whp_user.id, whp_user.name,whp_user.role')
            ->from('whp_user')
            ->where("whp_user.username='".$data['username']."' AND whp_user.password='".$data['password']."' AND whp_user.del='0' AND whp_user.status = 'Active'")
            ->get()->row_array();     

            if (isset($result['id']) && $result['id']) 
            {
                $result['user_type'] = 'sales';
            }
        }

        if(isset($data['mobile']))
        {
            $result = $this->db->select('whp_customer.id, whp_customer.name,whp_customer.mobile_1 as mobile')
            ->from('whp_customer')
            ->where("whp_customer.mobile_1='".$data['mobile']."' AND whp_customer.del='0'")
            ->get()->row_array();

            if (isset($result['id']) && $result['id']) 
            {
                $result['user_type'] = 'customer';
            }

        }

        if (isset($result['id']) && $result['id']) 
        {
            $payload = [
             'iat' => time(),
             'iss' => 'localhost',
             'id' => $result['id'],
            ];
            $result['otp']='123456';
            $token = $this->encode($payload, $this->secret_key);
            echo json_encode(array("data"=>$result, "token"=>$token));
        } 
        else 
        {
            $result = 'wrong';
            echo json_encode($result);
        }
    }


    public function login_customer()
    {
        $data = json_decode(file_get_contents("php://input"),true);

        $result = $this->db->select('whp_customer.id, whp_customer.name')
        ->from('whp_customer')
        ->where("whp_customer.mobile_1='".$data['mobile']."' AND whp_customer.del='0'")
        ->get()->row_array();
        
        // print_r($result);

        if (isset($result['id']) && $result['id']) 
        {
        
            $payload = [
             'iat' => time(),
             'iss' => 'localhost',
             'id' => $result['id'],
            ];
            
            $token = $this->encode($payload, $this->secret_key);
            echo json_encode(array("data"=>$result, "token"=>$token,'otp' => '12345'));
        } 
        else 
        {
            $result = 'wrong';
            echo json_encode($result);
        }
    }

}