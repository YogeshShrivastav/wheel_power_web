<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Arrival extends CI_Controller {

 public function __construct() {
    parent:: __construct();
    $this->load->database();
  }

	public function arrival_list() {

	    $this->db->select('DISTINCT(abq_product.product_name), abq_product.id, abq_product.cat_no, abq_product.price, abq_product.image,abq_product.id, abq_product_image.image as image');
		$this->db->from('abq_product');
		$this->db->join('abq_product_image', 'abq_product_image.product_id =abq_product.id','left');
		$this->db->where("abq_product.del ='0' ");
		$this->db->order_by("abq_product.id","desc ");
		$this->db->limit(15);
		$data =$this->db->get()->result_array();

		echo json_encode($data);

	}

	public function category_list()
	{
		
	    $this->db->select('DISTINCT(abq_product.category)');
		$this->db->from('abq_product');
		$this->db->where("abq_product.del ='0' ");
		$data =$this->db->get()->result_array();
		echo json_encode($data);

	}


	public function subcategory_data() {

		$_POST = json_decode(file_get_contents('php://input'),true);
 
			$val=$_POST['filterSelected']['category'];  
		if($val)
		{
			$this->db->select('DISTINCT(abq_product.sub_category)');
			$this->db->from('abq_product');
			$this->db->where_in('abq_product.category',$val);
			$this->db->where("abq_product.del ='0' ");
			$data['sub_category'] =$this->db->get()->result_array();
			//echo $this->db->last_query();die;
			if($data){
			
				 echo json_encode($data);
			}
			 
		}
		else
			 {
			 	$data=Array([0] => Array ([sub_category] =>''));
			 	echo json_encode($data);
			 }
}




}