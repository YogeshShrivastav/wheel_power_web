<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'APP_Controller.php';
class Customer extends APP_Controller {

	public function __construct() {
		parent:: __construct();
		$this->load->database();
	}

	public function app_version()
    {
    	$version = $this->db->select("version")->from("whp_app_version")->get()->row_array();

    	echo json_encode(array('app_version' => $version['version']));
    }

	public function submit_customer()
	{
		$_POST = json_decode(file_get_contents("php://input"),true);
		// print_r($_POST);
        $date_created = date('Y-m-d H:i:s');
        
        $exist_customer = $this->db->select("id")->from("whp_customer")->where("mobile_1",$_POST['mobile_1'])->where("del",'0')->get()->row_array();
        
		if(!$exist_customer)
		{
          	if(isset($_POST['mobile_2']) && $_POST['mobile_2'])
	        {  $mobile_2 = $_POST['mobile_2'];  }
	        else
	        { $mobile_2 = ""; }
	        
	        if(isset($_POST['email']) && $_POST['email'])
	        {  $email = $_POST['email'];  }
	        else
	        { $email = ""; }

	        if(isset($_POST['pincode']) && $_POST['pincode'])
	        {  $pincode = $_POST['pincode'];  }
	        else
	        { $pincode = ""; }

			$user_data = array(
	          'date_created' => $date_created,          
	          'name' => $_POST['name'],
	          'mobile_1' => $_POST['mobile_1'],
	          'mobile_2' => $mobile_2,
	          'email' => $email,
	          'address' => $_POST['address'],
	          'state' => $_POST['state'],
	          'district' => $_POST['district'],
	          'city' => $_POST['city'],
	          'pincode' => $pincode,
	          );
	      	$this->db->insert("whp_customer",$user_data);
          
      	}
        

      	echo json_encode("success");
	}

}