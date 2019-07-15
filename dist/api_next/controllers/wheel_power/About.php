<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends CI_Controller {

public function __construct() {
    parent:: __construct();
    $this->load->database();
  }
		  
	public function about_data() {

		$this->db->select('pages.content');
		$this->db->from('pages');
		$this->db->where("pages.page_name ='about_us' ");
		$this->db->where("pages.del ='0' ");
		$data = $this->db->get()->row_array();
		echo json_encode($data);

	}

public function contact_data() {

		$this->db->select('pages.content');
		$this->db->from('pages');
		$this->db->where("pages.page_name ='contact_us' ");
		$this->db->where("pages.del ='0' ");
		$data = $this->db->get()->row_array();
		echo json_encode($data);

	}


}