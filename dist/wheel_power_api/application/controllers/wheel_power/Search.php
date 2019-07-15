<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'APP_Controller.php';
class Search extends APP_Controller {

	public function __construct() {
		parent:: __construct();
		$this->load->database();
	}

	public function search_alldata() {

		$_POST = json_decode(file_get_contents('php://input'),true);
		
// 		$this->db->select('abq_product.product_name, abq_product.colors, abq_product.category, abq_product.sub_category, abq_product.cat_no, abq_product.price, abq_product.id, abq_product_feature.feature as feature, abq_product_feature.value as value, abq_product_feature.size as size');

	$this->db->select('abq_product.product_name, abq_product.id, abq_product_image.image as image, abq_product.sub_category,abq_product.price,abq_product.category,abq_product.cat_no');
		$this->db->from('abq_product');
// 		$this->db->join('abq_category', 'abq_category.category = abq_product.category','left');
		$this->db->join('abq_product_image', 'abq_product_image.product_id =abq_product.id','left');
		$this->db->or_where('abq_product.product_name LIKE "%'.$_POST['search'].'%"');
// 		$this->db->group_by('abq_product.product_name');
		$this->db->where('abq_product.del=0');
		$data = $this->db->get()->result_array();

		echo json_encode($data);
		
	}


	public function product_data() {

		$_POST = json_decode(file_get_contents('php://input'),true);
       
        // print_r($_POST);
        // exit;
		$this->db->select('whp_products.product_name, whp_products.colors, whp_products.category, whp_products.sub_category, whp_products.cat_no, whp_products.price, whp_products.id');
		$this->db->from('whp_products');
		$this->db->where("whp_products.cat_no ='".$_POST['cat_no']."'");
// 		$this->db->join('whp_product_feature', 'whp_product_feature.product_id =whp_products.id','left');
		$this->db->where('whp_products.del=0');

        $i=0;
		$data1 = $this->db->get()->row_array();
        foreach($data1 as $val)
        {
            $this->db->select("whp_product_feature.feature, whp_product_feature.value");
            $this->db->from("whp_product_feature");
            $this->db->where("whp_product_feature.id",$val['id']);
            $this->db->where("whp_product_feature.del","0");
            $this->db->group_by('whp_product_feature.feature');
            $feature = $this->db->get()->result_array();
            
            $data1[$i]['feature'] = $feature;
            $i++;
        }
        print_r($data1);
        exit;

// 		$this->db->select('image');
// 		$this->db->from('whp_product_image');
// 		$this->db->where("whp_product_image.product_id ='".$_POST."'");
// 		$this->db->where("whp_product_image.del ='0' ");
// 		$this->db->order_by("whp_product_image.id","desc ");
// 		$data2 =$this->db->get()->result_array();

// 		$result = array('productlist' => $data1,   'imageslist' => $data2 );


		echo json_encode($result);
		
	}


	public function get_brand()
	{
		
		$this->db->select('DISTINCT(brand)');
		$this->db->from('abq_product');
		$this->db->where("del ='0' ");
		$this->db->where("brand != '' ");
		$data =$this->db->get()->result_array();
		echo json_encode($data);

	}

}