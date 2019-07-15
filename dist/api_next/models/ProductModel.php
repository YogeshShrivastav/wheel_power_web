<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProductModel extends MY_Model
{
    public function decrypt($data)
	{
		$secret_key = 'my_simple_secret_key';
		$secret_iv = 'my_simple_secret_iv';

		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return $id=openssl_decrypt( base64_decode($data),$encrypt_method,$key,0,$iv);
	}

	public function encrypt($data)
	{		
		$secret_key = 'my_simple_secret_key';
		$secret_iv = 'my_simple_secret_iv';

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return $output = base64_encode( openssl_encrypt( $data, $encrypt_method, $key, 0, $iv ) );
	}
    
    public function get_products()
    {
        $this->db->select("whp_products.*");
        $this->db->from("whp_products");
        if(isset($_POST["search"]) && $_POST["search"] !='' )
        {
            if(isset($_POST["search"]["product_name"]))
            {
                if($_POST["search"]["product_name"]!=''){
                    $this->db->like('whp_products.product_name',$_POST["search"]["product_name"]);
                }
            }
            if(isset($_POST["search"]["cat_no"]))
            {
                if($_POST["search"]["cat_no"]!=''){
                    $this->db->like('whp_products.cat_no',$_POST["search"]["cat_no"]);
                }
            }
            if(isset($_POST["search"]["brand"]))
            {
                if($_POST["search"]["brand"]!=''){
                    $this->db->where('whp_products.brand',$_POST["search"]["brand"]);
                }
            }
            if(isset($_POST["search"]["category"]))
            {
                if($_POST["search"]["category"]!=''){
                  $this->db->where('whp_products.category',$_POST["search"]["category"]);   
                }
            }
            if(isset($_POST["search"]["sub_category"]))
            {
                if($_POST["search"]["sub_category"]!=''){
                $this->db->where('whp_products.sub_category',$_POST["search"]["sub_category"]);    
                }
            }
            
            if(isset($_POST["search"]["search_product"]))
            {
                 $this->db->group_start();
                 $this->db->like("whp_products.product_name",$_POST["search"]["search_product"]);
                 $this->db->or_like("whp_products.brand",$_POST["search"]["search_product"]);
                 $this->db->or_like("whp_products.category",$_POST["search"]["search_product"]);
                 $this->db->or_like('whp_products.cat_no',$_POST["search"]["search_product"]);
                 $this->db->group_end();
            }
        }
        $this->db->where("del","0");
        $this->db->order_by("whp_products.id", "DESC");
        $this->db->group_by("whp_products.cat_no");
        $tmp_db=clone $this->db;
        $num_row=$tmp_db->get()->num_rows();
        
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $result=$this->db->get()->result_array();
        $qty2=array();
        $qty1=array();
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
			
			$warehouse = $this->db->select("id")->from("whp_warehouse")->where('del',"0")->get()->result_array();
			$total = 0;
			foreach($warehouse as $data)
			{
			    $stock = $this->db->select("current_stock")->from("whp_warehouse_product")->where('warehouse_id',$data["id"])->where('cat_no',$v["cat_no"])->where('del','0')->get()->row_array();
			    $total = $total + (int)($stock['current_stock']);
			}
			$result[$i]['current_stock'] = $total;
		}
		
        $array=array("data"=>$result,"product_cn"=>$num_row);
        return $array;
    }
    
 
    
    public function get_productCategory()
    {
        $query=$this->db->distinct()->select('category')->from('whp_products')->where('del','0')->where("category!=''")->where('brand',$_POST)->get()->result_array();
        
        if(count($query) > 0)
        {
            $val = "category";
        }
        else
        {
            $val="product_code";
            $query=$this->db->distinct()->select('*')->from('whp_products')->where('del','0')->where('brand',$_POST)->get()->result_array();
        }
        return array("val_type" => $val,"value" => $query);
    }
    
    
    
    public function get_product_sub_category(){
       
        $query=$this->db->distinct()->select('sub_category')->where('del','0')->where('brand',$_POST["brand"])->where('category',$_POST["category"])->get('whp_products');
        return $query->result_array();
    }
    
    
    public function get_product_brand(){
        $query=$this->db->distinct()->select('brand')->where('del','0')->get('whp_products');
        return $query->result_array();
    }
    
    public function get_product_category_indp(){
        $query=$this->db->distinct()->select('category')->where('del','0')->where("category!=''")->get('whp_products');
        return $query->result_array();
    }
    
    public function get_product_sub_category_indp(){
        $query=$this->db->distinct()->select('sub_category')->where('del','0')->get('whp_products');
        return $query->result_array();
    }
    
    public function update_product($id)
    {
        $data=array(
            'product_name' => $this->input->post('product_name'),
            'brand' => $this->input->post('brand'),
            'category' => $this->input->post('category'),
            'sub_category' => $this->input->post('sub_category'),
            'cat_no' => $this->input->post('cat_no'),
            'price' => $this->input->post('price'),
            'colors' => $this->input->post('colors'),
            'unit_type' => $this->input->post('unit_type'),
            'description' => $this->input->post('description'),
            'del' => '0',
            'date_created' => date(dd-mm-yyyy)
        );
        if($id==0){
            return $this->db->insert('whp_products',$data);
        }else{
            $this->db->where('id',$id);
            return $this->db->update('whp_products',$data);
        }
    }

    public function delete_product($id){
        $data=array('del'=>'1');
        $this->db->where('id',$id)->update('whp_product_image',$data);
        $this->db->where('id',$id)->update('whp_product_feature',$data);
        return $this->db->where('id',$id)->update('whp_products',$data);
    }

}

?>