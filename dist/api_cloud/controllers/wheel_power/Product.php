<?php

error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include 'APP_Controller.php';
class Product extends APP_Controller {
	
	public function __construct() {
		parent:: __construct();
		$this->load->database();
    }
    
    public function get_brand()
    {
        $brand=$this->db->distinct()->select('whp_products.brand,whp_brand_image.image')->from('whp_products')->join("whp_brand_image","whp_brand_image.brand = whp_products.brand","left")->where('whp_products.brand!=','')->where('whp_products.del','0')->get()->result_array();
        echo json_encode($brand);
    }

	public function category_list()
	{
		$_POST = json_decode(file_get_contents('php://input'),true);
		
		$brand_str = '';
        $array = array();
        for($i=0;$i<count($_POST['brand']);$i++)
        {
            $array[]="whp_products.brand = '".$_POST['brand'][$i]."' ";
        }
        
        if(isset($array) && $array)
        {
            $start = '(';
            $brand = implode(' OR ',$array);
            $end = ')';
        }
        
        $brand_str = $start.$brand.$end;
		
		$this->db->select('DISTINCT(whp_products.category), whp_category_image.image as image');
		$this->db->from('whp_products');
		if(isset($brand_str) && $brand_str !='')
		{
		    $this->db->where($brand);
		}
		$this->db->join('whp_category_image', 'whp_category_image.category = whp_products.category','left');
		$this->db->where("whp_products.category !=''");
		$this->db->where("whp_products.del ='0' ");
		$data =$this->db->get()->result_array();

		echo json_encode($data);			
	}

	public function brandcategory_data()
	{
		$_POST = json_decode(file_get_contents('php://input'),true);

		$this->db->select('DISTINCT(abq_product.category)');
		$this->db->from('abq_product');		
		if(isset($_POST)){
        $this->db->where("abq_product.brand ='".$_POST."'");
		}
		
		$this->db->where("abq_product.del ='0' ");
		$data =$this->db->get()->result_array();

		echo json_encode($data);
	
	}

	public function subcategory_list() {

		$_POST = json_decode(file_get_contents('php://input'),true);
	
		$this->db->select('DISTINCT(whp_products.sub_category), whp_category_image.image as image');
		$this->db->from('whp_products');
		$this->db->join('whp_category_image', 'whp_category_image.category = whp_products.category','left');
		$this->db->where("whp_products.brand",$_POST["brand"]);
		$this->db->where("whp_products.category",$_POST["category"]);
		$this->db->where("whp_products.del ='0' ");
		$data =$this->db->get()->result_array();

		echo json_encode($data);

	}
	
	public function products_list()
	{
        $_POST = json_decode(file_get_contents('php://input'),true);

        $brand_str = '';
        $array = array();
        for($i=0;$i<count($_POST['brand']);$i++)
        {
            $array[]="whp_products.brand = '".$_POST['brand'][$i]."' ";
        }
        
        if(isset($array) && $array)
        {
            $start = '(';
            $brand = implode(' OR ',$array);
            $end = ')';
        }
        
        $brand_str = $start.$brand.$end;
        
        $this->db->select('whp_products.brand,whp_products.category,whp_products.sub_category,whp_products.product_name,whp_products.cat_no,whp_products.price,whp_product_image.image,whp_product_feature.feature,whp_product_feature.value');
        $this->db->from('whp_products');
        $this->db->join('whp_product_image','whp_product_image.product_id = whp_products.id','left');
        $this->db->join('whp_product_feature','whp_product_feature.product_id = whp_products.id','left');
        if(isset($brand_str) && $brand_str !='')
        {
            $this->db->where($brand_str);
        }
        if(isset($_POST["category"]) && $_POST["category"]!='all' )
        {
            $this->db->where("whp_products.category ='".$_POST["category"]."'");
        }
        if(isset($_POST['search']) && $_POST['search'])
        {
            $this->db->group_start();
            $this->db->like("whp_products.product_name",$_POST['search']);
            $this->db->or_like("whp_products.category",$_POST['search']);
            $this->db->or_like("whp_products.cat_no",$_POST['search']);
            $this->db->or_like("whp_products.brand",$_POST['search']);
            $this->db->group_end();
        }
		$this->db->where("whp_products.del ='0'");
		$this->db->group_by("whp_products.cat_no");
		$result =$this->db->get()->result_array();
		
        $i=0;
        foreach ($result as $key)
		{
            $warehouse = $this->db->select("id")->from("whp_warehouse")->where('del',"0")->get()->result_array();
            
			$total = 0;
			foreach($warehouse as $data)
			{
			    $stock = $this->db->select("current_stock")->from("whp_warehouse_product")->where('warehouse_id',$data["id"])->where('cat_no',$key["cat_no"])->where('del','0')->get()->row_array();
			    $total = $total + (int)($stock['current_stock']);
			}
			$result[$i]['current_stock'] = $total;
			$i++;
		}
 
		echo json_encode($result);
        
    }

	public function product_list() {	

		$postData = json_decode(file_get_contents('php://input'),true);

		$query_str = $typeSeparator = '';
		if(isset($postData['filterSelectedData']) && $postData['filterSelectedData']) 
		{
			foreach($postData['filterSelectedData'] as $type_arr)
			{
				if($query_str)
				{
					$typeSeparator = " OR ";
				}
				
				if(COUNT($type_arr['sub_category']) > 0)
				{
					$cnt = 0;
					foreach($type_arr['sub_category'] as $subCat) 
					{
						if($cnt===0) 
						{
							$add_str = $typeSeparator;
						} 
						else 
						{
							$add_str = " OR ";
						}

						$query_str .= $add_str."(category = '".$type_arr['category']."' AND sub_category = '".$subCat."')";
						$cnt++;
					}

				} 
				else 
				{
					$query_str .= $typeSeparator."(category = '".$type_arr['category']."')";
				}
			}
		}

		if($query_str) 
		{
			$this->db->select('DISTINCT(whp_products.product_name), whp_products.category, whp_products.sub_category, whp_products.cat_no, whp_products.price, whp_products.id, whp_product_image.image as image');
			$this->db->from('whp_products');
			$this->db->join('whp_product_image', 'whp_product_image.product_id =whp_products.id','left');
			$this->db->where("(".$query_str.")");
			$this->db->where("whp_products.del ='0' ");
			$this->db->order_by("whp_products.id","desc ");
			$data = $this->db->get()->result_array();

			echo json_encode($data);


		} 
		else 
		{
		    $this->db->select('whp_products.product_name,whp_products.cat_no,whp_products.price,whp_product_image.image');
            $this->db->from('whp_products');
            $this->db->join('whp_product_image','whp_product_image.product_id = whp_products.id','left');
            $this->db->where("whp_products.brand ='".$postData["brand"]."'");
            $this->db->where("whp_products.category ='".$postData["category"]."'");
    		$this->db->where("whp_products.sub_category ='".$postData["sub_category"]."'");
    		$this->db->where("whp_products.del ='0' ");
    		$data =$this->db->get()->result_array();
		    
		    echo json_encode($data);

		}
	}


	public function subcategory_data() {

		$_POST = json_decode(file_get_contents('php://input'),true);

		$val=$_POST['category'];  
		if($val)
		{
			$this->db->select('DISTINCT(whp_products.sub_category),whp_products.category');
			$this->db->from('whp_products');
			$this->db->where_in('whp_products.category',$val);
			$this->db->where("whp_products.del ='0'");
			$data=$this->db->get()->result_array();
			
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

    public function product_detail()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);
        
        $this->db->select("*");
        $this->db->from("whp_products");
        $this->db->where("cat_no",$_POST);
        $this->db->where("del","0");
        
        $product = $this->db->get()->row_array();
        
        $this->db->select("image");
        $this->db->from("whp_product_image");
        $this->db->where("product_id",$product["id"]);
        $this->db->where("del","0");
        
        $image = $this->db->get()->result_array();
        $product['image'] = $image;
        
        $this->db->select("feature,value");
        $this->db->from("whp_product_feature");
        $this->db->where("product_id",$product["id"]);
        $this->db->where("del","0");
        
        $feature = $this->db->get()->result_array();
        $product['feature'] = $feature;
        
        $this->db->select("feature");
        $this->db->from("whp_product_feature");
        $this->db->where("product_id",$product["id"]);
        $this->db->where("del","0");
        $this->db->group_by('feature');
        
        $feature_type = $this->db->get()->result_array();
        $product['feature_type'] = $feature_type;
        
        echo json_encode(array("product_data" => $product));
    }

     public function get_warehouse_vise()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("whp_products.cat_no,whp_products.id");
        $this->db->from("whp_products");
        $this->db->where("whp_products.cat_no",$data);
        $this->db->where("whp_products.del","0");
        $product = $this->db->get()->row_array();
        
        $warehouse_product = $this->db->select("whp_warehouse_product.date_created,whp_warehouse_product.id,whp_warehouse_product.cat_no,whp_warehouse_product.current_stock,whp_warehouse.warehouse_name,whp_warehouse.address,whp_manufacturer.name as vendor_name")
        ->from("whp_warehouse_product")
        ->join("whp_warehouse","whp_warehouse.id = whp_warehouse_product.warehouse_id","left")
        ->join("whp_receive_item_detail","whp_receive_item_detail.product_id = '".$product['id']."' ","left")
        ->join('whp_receive_item',"whp_receive_item.id = whp_receive_item_detail.receive_item_id","left")
        ->join("whp_manufacturer","whp_manufacturer.id = whp_receive_item.vendor_id","left")
        ->where("whp_warehouse_product.cat_no",$product['cat_no'])
        ->group_by("whp_warehouse.id")
        ->where("whp_warehouse_product.del","0")
        ->get()->result_array();
          
        echo json_encode(array("warehouse_product"=>$warehouse_product));
        
    }
  
    
}