<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class warehouse extends MY_Controller {


    public function __construct() {
        parent:: __construct();
        $this->load->model('manufacturerModel');
        $this->load->database();
    }
    
    public function submit_warehouse()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $date_created = date('Y-m-d H:i:s');
        
        if(isset($data['location']) && $data['location'])
        { $location = $data['location']; }
        else
        { $location = ""; }
        
        if(isset($data['email']) && $data['email'])
        { $email = $data['email']; }
        else
        { $email = ""; }
        
        if(isset($data['mobile2']) && $data['mobile2'])
        { $mobile2 = $data['mobile2']; }
        else
        { $mobile2 = ""; }
        
        if(isset($data['mobile1']) && $data['mobile1'])
        { $mobile1 = $data['mobile1']; }
        else
        { $mobile1 = ""; }
        
        if(isset($data['landline']) && $data['landline'])
        { $landline = $data['landline']; }
        else
        { $landline = ""; }
        
        if(isset($data['address']) && $data['address'])
        { $address = $data['address']; }
        else
        { $address = ""; }
        
        if(isset($data['state']) && $data['state'])
        { $state = $data['state']; }
        else
        { $state = ""; }
        
        if(isset($data['district']) && $data['district'])
        { $district = $data['district']; }
        else
        { $district = ""; }
        
        if(isset($data['city']) && $data['city'])
        { $city = $data['city']; }
        else
        { $city = ""; }
        
        if(isset($data['pincode']) && $data['pincode'])
        { $pincode = $data['pincode']; }
        else
        { $pincode = ""; }
        
        $array = array(
        'date_created' => $date_created,
        'created_by' => $data['created_by'],
        'warehouse_name' => $data['warehouse_name'],
        'contact_person' => $data['contact_person'],
        'mobile1' => $mobile1,
        'mobile2' => $mobile2,
        'landline' => $landline,
        'email' => $email,
        'address' => $address,
        'country_id' => $data['country_id'],
        'state' => $state,
        'district' => $district,
        'city' => $city,
        'pincode' => $pincode,
            );
        
        if(isset($data['id']) && $data['id'])
        {
            $this->db->where("whp_warehouse.id",$data['id']);
            $this->db->update("whp_warehouse",$array);
            
            $msg = "success";
        }
        else
        {
            $this->db->insert("whp_warehouse",$array);
            
            $inserted_id = $this->db->insert_id();
            
            $username = 'wh'.$inserted_id;
            
            $update = array(
                
            'username' => $username,
            'password' => $mobile1
            
            );
            
            $updated = $this->db->where("whp_warehouse.id",$inserted_id);
            $this->db->update('whp_warehouse',$update);
        
            if($updated)
            {
                $msg = "success";
            }
            else
            {
                $msg = "error";
            }
        }
        
        echo json_encode(array("msg" => $msg));
    }
    
    
    public function warehouse_list()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("whp_warehouse.*");
        $this->db->from("whp_warehouse");
        if(isset($data['search']) && $data['search'])
        {
            if(isset($data['search']['search_name']) && $data['search']['search_name'])
            {
                $this->db->group_start();
                $this->db->or_like("whp_warehouse.warehouse_name",$data['search']['search_name']);
                $this->db->or_like("whp_warehouse.contact_person",$data['search']['search_name']);
                $this->db->or_like("whp_warehouse.username",$data['search']['search_name']);
                $this->db->group_end();
            }
        }
        
        $this->db->where("whp_warehouse.del","0");
        $this->db->order_by("whp_warehouse.id","desc");
        $tmp_db = clone $this->db;
        if(isset($data["pagelimit"]) && $data["pagelimit"]!=0)
        {
            $this->db->limit(@$data["pagelimit"],@$data["start"]);
        }
        
        $warehouse = $this->db->get()->result_array();
        $total_rec = $tmp_db->get()->num_rows();
        
        foreach ($warehouse as $i => $v)
		{
			$warehouse[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        
        $result = array("warehouse_list" => $warehouse,"total_rec" => $total_rec);
        echo json_encode($result);
    }
    
    public function delete()
    {
        $data = json_decode(file_get_contents("php://input"),true);

        $this->db->query("update whp_warehouse set del='1' where id = '".$data."' ");
        
        echo json_encode("deleted");
    }
    
    public function warehouse_edit()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $warehouse = $this->db->get_where('whp_warehouse', array('id'=>$this->decrypt($data)))->row_array();
        
        echo json_encode($warehouse);
    }
    
    public function warehouse_product_list()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $sele_str='';
        if(!isset($data['warehouse_id']) || !$data['warehouse_id'])
        {
            $sele_str = ", SUM(whp_warehouse_product.current_stock) as current_stock";
        }
        
        $this->db->select("whp_warehouse_product.*,whp_products.min_qty as stock_alert,whp_products.master_packing ".$sele_str." ");
        $this->db->from("whp_warehouse_product");
        if(isset($data['warehouse_id']) && $data['warehouse_id'])
        {
            $this->db->where("whp_warehouse_product.warehouse_id",$data['warehouse_id']);
        }
        $this->db->join("whp_products","whp_products.cat_no=whp_warehouse_product.cat_no","left");
        if(isset($data["search"]) && $data["search"] !='' )
        {
            if(isset($data["search"]["product_name"]))
            {
                if($data["search"]["product_name"]!=''){
                    $this->db->like('whp_warehouse_product.product_name',$data["search"]["product_name"]);
                }
            }
            if(isset($data["search"]["cat_no"]))
            {
                if($data["search"]["cat_no"]!=''){
                    $this->db->like('whp_warehouse_product.cat_no',$data["search"]["cat_no"]);
                }
            }
            if(isset($data["search"]["brand"]))
            {
                if($data["search"]["brand"]!=''){
                    $this->db->where('whp_warehouse_product.brand',$data["search"]["brand"]);
                }
            }
            if(isset($data["search"]["category"]))
            {
                if($data["search"]["category"]!=''){
                  $this->db->where('whp_warehouse_product.category',$data["search"]["category"]);   
                }
            }
          
            
            if(isset($data["search"]["search_product"]))
            {
                 $this->db->group_start();
                 $this->db->like("whp_warehouse_product.product_name",$data["search"]["search_product"]);
                 $this->db->or_like("whp_warehouse_product.brand",$data["search"]["search_product"]);
                 $this->db->or_like("whp_warehouse_product.category",$data["search"]["search_product"]);
                 $this->db->or_like("whp_warehouse_product.sub_category",$data["search"]["search_product"]);
                 $this->db->group_end();
            }
            
            if(isset($data["search"]["stock_type"]))
            {
                if($data["search"]["stock_type"] == 'instock')
                {
                    $this->db->where('whp_warehouse_product.current_stock != ','0');
                    $this->db->where("whp_warehouse_product.current_stock > whp_products.min_qty");
                }
                else if($data["search"]["stock_type"] == 'stockalert')
                {
                    $this->db->where('whp_warehouse_product.current_stock != ','0');
                    $this->db->where("whp_warehouse_product.current_stock <= whp_products.min_qty");
                }
                else if($data["search"]["stock_type"] == 'outofstock')
                {
                    $this->db->where('whp_warehouse_product.current_stock','0');
                }
            }
           
        }
        $this->db->where("whp_warehouse_product.del","0");
        $this->db->group_by("whp_warehouse_product.cat_no");
        $this->db->order_by("whp_warehouse_product.id", "DESC");
        
        $tmp_db=clone $this->db;
        
        $num_row=$tmp_db->get()->num_rows();
        
        if(isset($_POST["pagelimit"]) && $_POST["pagelimit"]!=0)
        {
            $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        }
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_cat'] =  $this->encrypt($v["cat_no"]);
		}
		
		$this->db->select("id");
        $this->db->from("whp_warehouse_product");
        $this->db->where("del","0");
        if(isset($data['warehouse_id']) && $data['warehouse_id'])
        {
            $this->db->where("warehouse_id",$data['warehouse_id']);
        }
         else
        {
            $this->db->group_by("whp_warehouse_product.cat_no");
        }
        $this->db->where('current_stock','0');
        $this->db->where('del','0');
        $out_of_stock_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_warehouse_product.cat_no");
        $this->db->from("whp_warehouse_product");
        $this->db->join("whp_products","whp_products.cat_no=whp_warehouse_product.cat_no","left");
        $this->db->where("whp_warehouse_product.del","0");
        $this->db->where('whp_warehouse_product.current_stock != ','0');
        $this->db->where("whp_warehouse_product.current_stock > whp_products.min_qty");
        if(isset($data['warehouse_id']) && $data['warehouse_id'])
        {
            $this->db->where("warehouse_id",$data['warehouse_id']);
        }
        else
        {
            $this->db->group_by("whp_warehouse_product.cat_no");
        }
        $in_stock_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_warehouse_product.cat_no");
        $this->db->from("whp_warehouse_product");
        $this->db->join("whp_products","whp_products.cat_no=whp_warehouse_product.cat_no","left");
        $this->db->where("whp_warehouse_product.del","0");
        $this->db->where('whp_warehouse_product.current_stock != ','0');
        $this->db->where("whp_warehouse_product.current_stock <= whp_products.min_qty");
        if(isset($data['warehouse_id']) && $data['warehouse_id'])
        {
            $this->db->where("warehouse_id",$data['warehouse_id']);
        }
        else
        {
            $this->db->group_by("whp_warehouse_product.cat_no");
        }
        $stock_alert_cn = $this->db->get()->num_rows();
        
        $array=array("data"=>$result,"product_cn"=>$num_row,"out_st_cn" => $out_of_stock_cn,"in_st_cn" => $in_stock_cn,"st_al_cn" => $stock_alert_cn);
        // print_r($array);
        
        echo json_encode($array);
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
  
    public function stock_warehouse_list()
    {
        $data = json_decode(file_get_contents("php://input"),true);
  
        $this->db->select("whp_warehouse.id,whp_warehouse.warehouse_name");
        $this->db->from("whp_warehouse");
        if(isset($data['warehouse_id']) && $data['warehouse_id'])
        {
            $this->db->where("whp_warehouse.id!=",$data['warehouse_id']);
        }
        $this->db->where("whp_warehouse.del","0");
        $warehouse = $this->db->get()->result_array();
        
        echo json_encode(array('warehouse_list'=>$warehouse));
    }
    
    public function stock_transfer_brand()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $brand = $this->db->DISTINCT()->select("brand")->from("whp_warehouse_product")->where("warehouse_id",$data['warehouse_id'])->get()->result_array();
       
        echo json_encode(array('brand' => $brand));
    }
    
    public function stock_transfer_category()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->DISTINCT();
        $this->db->select("category");
        $this->db->from("whp_warehouse_product");
        if(isset($data['search']['brand']) && $data['search']['brand'])
        { 
            $this->db->where("brand", $data['search']['brand']);
        }
        $this->db->where("warehouse_id",$data['warehouse_id']);
        $category = $this->db->get()->result_array();
       
        echo json_encode(array('category' => $category));
    }
    
    public function stock_transfer_product()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->DISTINCT();
        $this->db->select("*");
        $this->db->from("whp_warehouse_product");
        if(isset($data['search']['brand']) && $data['search']['brand'])
        {
            $this->db->where("brand",$data['search']['brand']);
        }
        if(isset($data['search']['category']) && $data['search']['category'])
        {
            $this->db->where("category",$data['search']['category']);
        }
        $this->db->where("warehouse_id",$data['warehouse_id']);
        $this->db->where("current_stock!=","0");
        
        $product = $this->db->get()->result_array();
       
        echo json_encode(array('product_list' => $product));
    }
}