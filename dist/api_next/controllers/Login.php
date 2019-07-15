<?php
date_default_timezone_get("asia/kolkata");
defined('BASEPATH') OR exit('No direct script access allowed');

header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
// include('APP_Controller.php');

class Login extends CI_Controller 
{
	public function __construct()
	{
		parent:: __construct();
		$this->load->database();
	}

	public function index()
	{
		$this->load->view('index');
    }
	
	public function loginUser()
	{
		$data=json_decode(file_get_contents("php://input"),true);
		
		$this->db->select("id,mobile,name");
		$this->db->from("whp_user");
		$this->db->where("username",$data["username"]);
		$this->db->where("password",$data["password"]);
		$this->db->where("del","0");
		$result=$this->db->get()->row_array();
		
		$login_type = "admin";
		
		if(!$result)
		{
		   	$this->db->select("id,mobile1,contact_person as name");
    		$this->db->from("whp_warehouse");
    		$this->db->where("username",$data["username"]);
    		$this->db->where("password",$data["password"]);
    		$this->db->where("del","0");
    		$result=$this->db->get()->row_array();
    		
    		$login_type = "wharehouse";
		}
		
		$array = array("login_type"=> $login_type,"data" => $result);
        
		echo json_encode($array);
	}	

	public function hello()
	{
		echo "hello";
	}
}