<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class products extends MY_Controller {
    
    
    public function __construct() {
      parent:: __construct();
      $this->load->model('productModel');
      $this->load->database();
    }
    

    public function get()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
       
        $products=new ProductModel;
        $data=$products->get_products();
      
        echo json_encode($data);
    }
    
    public function check_product()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        
        $product = $this->db->select("id")->from("whp_products")->where("cat_no",$data)->where("cat_no",$data)->get()->num_rows();
        
        if($product > 0)
        {
            $exist = true;
        }
        else
        {
            $exist = false;
        }
        
        echo json_encode(array('exist' => $exist));
    }
  
    public function get_product_category(){
        
        $_POST=json_decode(file_get_contents("php://input"),true);
       
        $products=new ProductModel;
        $data=$products->get_productCategory();
        echo json_encode($data);
    }
    public function get_product_sub_category(){
        
        $_POST=json_decode(file_get_contents("php://input"),true);
         
        $products=new ProductModel;
        $data=$products->get_product_sub_category();
        echo json_encode($data);
    }
    public function get_product_brand(){
        
        $products=new ProductModel;
        $data=$products->get_product_brand();
        echo json_encode($data);
    }
    
    public function get_product_category_indp(){
        
        $products=new ProductModel;
        $data=$products->get_product_category_indp();
        $i=0;
        echo json_encode($data);
    }
    
    public function get_product_sub_category_indp(){
        
        $_POST=json_decode(file_get_contents("php://input"),true);
         
        $products=new ProductModel;
        $data=$products->get_product_sub_category_indp();
        echo json_encode($data);
    }
    
     
    public function store()
    {
        $data=$_POST;
        
        $features = json_decode($data['feature']);
        
        if(isset($data['description']))
        {  $desc=str_replace('"', '', $data['description']); }
        else
        {  $desc=''; } 
        
         if(isset($data['cur_stock']))
        {  $cur_stock=str_replace('"', '', $data['cur_stock']); }
        else
        {  $cur_stock=''; } 
        
        $datains = array(
            'product_name' => trim(str_replace('"', '', $data['product_name'])),
            'brand' => strtoupper(trim(str_replace('"', '', $data['brand']))),
            'category' => strtoupper(trim(str_replace('"', '', $data['category']))),
            'sub_category' => strtoupper(trim(str_replace('"', '', $data['subcategory']))),
            'cat_no' => trim(str_replace('"', '', $data['product_code'])),
            'price' => json_decode($data['price']),
            'unit_type' => str_replace('"', '', $data['unit']),
            'master_packing' => str_replace('"', '', $data['master_packing']),
            'current_stock' => str_replace('"', '', $cur_stock),
            'created_by' => json_decode($data['user_type']),
            'min_qty' => json_decode($data['min_qty']),
            'description' => $desc,
            'del' => '0',
            'date_created' => date('Y-m-d | h:i:sa')
        );
        
        $datafinal = $this->db->insert('whp_products', $datains);
        $lid = $this->db->insert_id();
        if(isset($features))
        {
            $this->insert_product_feature($features,$lid);
        }
        if(isset($_FILES))
        {
            $this->insert_product_image($_FILES,$lid);
        }
        if($datafinal)
        {
            echo json_encode($datafinal);
        }
        else
        {
            echo json_encode("Unsuccess");
        }
    }
    
    public function insert_product_feature($dataf,$id){
      
        foreach($dataf as $dt){
          
             $val=explode(",",$dt->value);
             foreach($val as $vl){
                $data = array(
                    'product_id' => $id,
                    'feature' => $dt->type,
                    'value' => $vl,
                    'del' => '0',
                    'date_created' => date('Y-m-d | h:i:sa')
                );
                $data = $this->db->insert('whp_product_feature', $data);
                $data = array();
             }
             $val = array();
        }
        return true;
    }
    
    public function insert_product_image($imgfiles,$id){
        
        foreach($_FILES as $files){
            $type=$files["type"];
            $name=$files["name"];
          	$uploaded_path = $_SERVER['DOCUMENT_ROOT'].'/wheel_power_new/wheel_power_api/'. DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR ;
    	    $tmp_name=$files["tmp_name"];
    		$img_name="image_of_product_id_".$id."_".uniqid();
    		$tmp_ext=explode(".",$name);
    		$ext=strtolower(end($tmp_ext));
    		$uploaded_path.=$img_name;
    		$uploaded_path.=".".$ext;
    
    		if($type=="image/jpg"||$type=="image/png"||$type=="image/jpeg")
    		{
    			if(move_uploaded_file($tmp_name, $uploaded_path)){
    			   $this->save_image($img_name.".".$ext,$id); 
    			}
    		}
          }
    		return true;
	}
    
    public function save_image($img,$p_id){
         $data = array(
                'product_id' => $p_id,
                'image' => $img,
                'del' => '0',
                'date_created' => date('Y-m-d | h:i:sa')
            );
         $data = $this->db->insert('whp_product_image', $data);
         return true;
    }
    public function edit()
    {
        
        $_POST=json_decode(file_get_contents("php://input"),true);
        $product = $this->db->select("whp_products.*");
        $product = $this->db->from("whp_products");
        $product = $this->db->where('whp_products.id',$this->decrypt($_POST['id']));
        $product = $this->db->where('whp_products.del','0');
        $product = $this->db->get()->row_array();
        
        $pro_feature = $this->db->select("whp_product_feature.*");
        $pro_feature = $this->db->from("whp_product_feature");
        $pro_feature = $this->db->where('whp_product_feature.product_id',$this->decrypt($_POST['id']));
        $pro_feature = $this->db->where('whp_product_feature.del','0');
        $pro_feature = $this->db->get()->result_array();
        
        $pro_feature_list = $this->db->select("whp_product_feature.feature");
        $pro_feature_list = $this->db->distinct();
        $pro_feature_list = $this->db->from("whp_product_feature");
        $pro_feature_list = $this->db->where('whp_product_feature.product_id',$this->decrypt($_POST['id']));
        $pro_feature_list = $this->db->where('whp_product_feature.del','0');
        $pro_feature_list = $this->db->get()->result_array();
        
        $pro_img = $this->db->select("whp_product_image.*");
        $pro_img = $this->db->from("whp_product_image");
        $pro_img = $this->db->where('whp_product_image.product_id',$this->decrypt($_POST['id']));
        $pro_img = $this->db->where('whp_product_image.del','0');
        $pro_img = $this->db->get()->result_array();
        
        $final_pro = array('product' => $product,'feature' => $pro_feature,'feature_list'=> $pro_feature_list,'image' => $pro_img );
        echo json_encode($final_pro );
    
    }
    public function update($id)
    {
        $products=new ProductsModel;
        $products->update_product($id);
        return $products;
    }
    public function delete()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        $this->db->query("update whp_product_feature set del=1 where product_id='".$data."'");
        $this->db->query("update whp_product_image set del=1 where product_id='".$data."'");
		$sql=$this->db->query("update whp_products set del=1 where id='".$data."'");

		if($sql)
		{
			$msg="success";
		}
		else{
			$msg="error";
		}

		$result=array("msg" =>$msg);

		echo json_encode($result);
    }
    
    public function add_image()
    {
        $products=new ProductModel;
        $id=$products->decrypt($_POST["product_id"]);
        
        if(isset($_FILES)){
          $res=$this->insert_product_image($_FILES,$id);
        }
        if($res)
        {
            echo json_encode("success");
        }
        else
        {
            echo json_encode("error");
        }
    }
    
    public function delete_image()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        
        $res=$this->db->query("update whp_product_image set del=1 where id='".$data."'");
        echo json_encode($res);
    }
    
    public function feature_add(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        foreach($_POST as $vl){
                $data = array(
                    'product_id' => $this->decrypt($vl['product_id']),
                    'feature' => $vl['feature'],
                    'value' => $vl['value'],
                    'del' => '0',
                    'date_created' => date('Y-m-d | h:i:sa')
                );
                $data = $this->db->insert('whp_product_feature', $data);
                $data = array();
             }
        echo json_encode($data);
    }
    public function remove_feature_value(){
        
        $data=json_decode(file_get_contents("php://input"),true);
        $sql=$this->db->query("update whp_product_feature set del=1 where id='".$data."'");
        echo json_encode($sql);
    }
    public function remove_feature(){
        
        $_POST=json_decode(file_get_contents("php://input"),true);
        $data=array(
            'del' => 1
         );
         $this->db->where('whp_product_feature.product_id',$this->decrypt($_POST['product_id']));
         $this->db->where('whp_product_feature.feature',$_POST['feature']);
         $data=$this->db->update('whp_product_feature',$data);
         echo json_encode($data);
    }
    public function add_feature_value(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        $data = array(
                    'product_id' => $_POST['product_id'],
                    'feature' => $_POST['type'],
                    'value' => $_POST['value'],
                    'del' => '0',
                    'date_created' => date('Y-m-d | h:i:sa')
                );
        $data = $this->db->insert('whp_product_feature', $data);
        echo json_decode($data);
    }
    
    public function update_attribute(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        $total_wr_qty=0;
     
        if($_POST["attribute_name"] == 'Warehouse A' || $_POST["attribute_name"] == 'Warehouse B')
        {
            // print_r($_POST);
            $val2=0;
            $val1=0;
            
            $sel_wr=$this->db->select("id")->from("whp_stock_warehouse")->where('warehouse_name',$_POST["attribute_name"])->where("product_id",$this->decrypt($_POST['product_id']))->get()->num_rows();
            
            if($sel_wr > 0)
            {
                $array=array(
                   'qty' => $_POST["value"]
                );
                $this->db->where('warehouse_name',$_POST["attribute_name"]);
                $this->db->where('product_id',$this->decrypt($_POST['product_id']));
                $this->db->update('whp_stock_warehouse',$array);
            }
            else
            {
                $array=array(
                    'warehouse_name' => $_POST["attribute_name"],
                    'qty' => $_POST["value"],
                    'product_id' => $this->decrypt($_POST['product_id'])
                );
                
                $this->db->insert("whp_stock_warehouse",$array);
            }
            
            $select=$this->db->select("qty")->from("whp_stock_warehouse")->where("product_id",$this->decrypt($_POST['product_id']))->get()->result_array();
            
            if(isset($select[0]['qty']))
            {
                $val1=$select[0]['qty'];
            }
            else
            {
                $val1=0;
            }
            
            if(isset($select[1]['qty']))
            {
                $val2=$select[1]['qty'];
            }
            else
            {
                 $val2=0;
            }
            
            $total_wr_qty=$val1+$val2;
            
            $data=array(
                'warehouse_qty' => $total_wr_qty
            );
            
            $this->db->where('id',$this->decrypt($_POST['product_id']));
            $data=$this->db->update('whp_products',$data);
        }
        else
        {
            $data=array(
                $_POST['attribute_name'] => $_POST['value']
            );
            $this->db->where('id',$this->decrypt($_POST['product_id']));
            $data=$this->db->update('whp_products',$data);
        }
        
        echo json_encode($data);
    }
    
////////////////////////////////////////// MY CODE /////////////////////////////////////////////////


    public function get_products_for_update()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
       
        $products=new ProductModel;
        $product_data=$products->get_products();
        
        $this->db->select("*");
        $this->db->from("whp_warehouse_product");
        $this->db->where("warehouse_id",$_POST['warehouse_id']);
        $this->db->where("del","0");
        $warehouse_product = $this->db->get()->result_array();
        
        // print_r($warehouse_product);
        for($i=0;$i<count($product_data['data']);$i++)
        {
            for($j=0;$j<count($warehouse_product);$j++)
            {
                if($product_data['data'][$i]['cat_no'] == $warehouse_product[$j]['cat_no'])
                {
                    $product_data['data'][$i]['current_stock'] = $warehouse_product[$j]['current_stock'];
                }
            }
        }
        // print_r($product_data);
        
        echo json_encode($product_data);
    }
    
    
    public function insert_brand_image()
    {
        $type=$_FILES['image']["type"];
        $name=$_FILES['image']["name"];
      	$uploaded_path = $_SERVER['DOCUMENT_ROOT'].'/wheel_power_new/wheel_power_api/'. DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR ;
	    $tmp_name=$_FILES['image']["tmp_name"];
		$img_name=uniqid();
		$tmp_ext=explode(".",$name);
		$ext=strtolower(end($tmp_ext));
		$uploaded_path.=$img_name;
		$uploaded_path.=".".$ext;

		if($type=="image/jpg"||$type=="image/png"||$type=="image/jpeg")
		{
			move_uploaded_file($tmp_name, $uploaded_path);
			
		}
        
        $result = $this->db->query("select id from whp_brand_image where brand='".json_decode($_POST['brand'])."' ")->row_array();
        
        if(isset($result['id']) && $result['id'])
        {
            $update = array(
                'image' => $img_name.".".$ext
            );
            
            $this->db->where("id",$result['id']);
            $this->db->update('whp_brand_image', $update);
        }
        else
        {
            $data = array(
                'brand' => json_decode($_POST['brand']),
                'image' => $img_name.".".$ext
            );
            $data = $this->db->insert('whp_brand_image', $data);
        }
        
        echo json_encode("success");
    }
    
}
?>
