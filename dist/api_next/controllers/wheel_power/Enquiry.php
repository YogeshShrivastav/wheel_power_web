<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enquiry extends CI_Controller {

	public function __construct() {
		parent:: __construct();
		$this->load->database();
	}

	public function all_state() {

		$this->db->select('DISTINCT(abq_postal_master.state_name)');
		$this->db->from('abq_postal_master');
		$this->db->where("abq_postal_master.del ='0' ");
		$data =$this->db->get()->result_array();
		echo json_encode($data);

	}

	public function all_city() {

		$_POST = json_decode(file_get_contents('php://input'),true);

		$this->db->select('DISTINCT(abq_postal_master.district_name)');
		$this->db->from('abq_postal_master');
		$this->db->where("abq_postal_master.state_name='".$_POST."'");
		$this->db->where("abq_postal_master.del ='0' ");
		$data = $this->db->get()->result_array();
		echo json_encode($data);

	}


	public function submit_enquiry() {

		$_POST = json_decode(file_get_contents('php://input'),true);

		if(isset($_POST['email']) && $_POST['email']) {

			$query="INSERT INTO `abq_enquiry`(`fname`, `lname` , `phone`, `email`,  `enquiry`,  `user`,`state`, `district`, `com_name`, `del`) VALUES('".$_POST['fname']."', '".$_POST['lname']."', '".$_POST['phone']."', '".$_POST['email']."', '".$_POST['enquiry']."', '".$_POST['user']."','".$_POST['state']."', '".$_POST['city']."','".$_POST['company']."', '0') ";
			$res=$this->db->query($query);

			if($res){

				echo json_encode(array('message' => "SUCCESS"));

			}

			
			
		}

		

	}


}