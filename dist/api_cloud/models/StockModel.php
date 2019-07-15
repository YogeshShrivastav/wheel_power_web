<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class StockModel extends MY_Model
{
    
    public function get_all_stock_return(){
        // print_r($_POST);
        // exit;
        $this->db->select("whp_stock_transaction.invoice_value,whp_stock_transaction.invoice_date,whp_stock_transaction.manufacturer_id,whp_stock_transaction.id,whp_manufacturer.name");
        $this->db->from("whp_stock_transaction");
        $this->db->join("whp_manufacturer","whp_stock_transaction.manufacturer_id=whp_manufacturer.id","left");
        $this->db->where("whp_stock_transaction.type",$_POST["type"]);
        if(isset($_POST["search"]["search_name"]))
        {
            $this->db->like("whp_manufacturer.name",$_POST["search"]["search_name"]);
        }
        $this->db->where("whp_stock_transaction.del","0");
        $this->db->group_by("whp_stock_transaction.id");
        $this->db->order_by("whp_stock_transaction.id", "DESC");
        
        $tmp_db=clone $this->db;
        $row=$tmp_db->get()->num_rows();
        
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["manufacturer_id"]);
			$this->db->select("whp_stock_transaction_detail.*,whp_products.cat_no");
			$this->db->from("whp_stock_transaction_detail");
			$this->db->join("whp_products","whp_stock_transaction_detail.product_id=whp_products.id","left");
			$this->db->where("whp_stock_transaction_detail.stock_transaction_id",$v["id"]);
			$this->db->where("whp_stock_transaction_detail.del","0");
			$product=$this->db->get()->result_array();
			
			$result[$i]["product"]=$product;
		}
		
		
		$array=array("data"=>$result,"row_cn"=>$row);
        return $array;
    }
    
    public function get_stock(){
        
        // print_r($_POST);
        // exit;
        if(!empty($this->input->get("search"))){
            $this->db->like('date', $this->input->get("search"));
        }
        // $query = $this->db->get("whp_products");
        $this->db->select("*");
        $this->db->join("whp_stock_transaction_detail","whp_stock_transaction.id=whp_stock_transaction_detail.stock_transaction_id","left");
        $this->db->from(" whp_stock_transaction");
        $this->db->where("whp_stock_transaction.type",$_POST["type"]);
        $this->db->where("whp_stock_transaction_detail.product_id",@$_POST["product_id"]);
        $this->db->where("whp_stock_transaction.del","0");
        $this->db->order_by("whp_stock_transaction.id", "DESC");
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        return $result;
    }
    
    public function incoming_stock()
    {
        $id=$this->decrypt($_POST);
        // print_r($id);
        $this->db->select("whp_stock_transaction_detail.*,whp_stock_transaction.manufacturer_id,whp_stock_transaction.invoice_no,whp_stock_transaction.invoice_value,whp_stock_transaction.invoice_date,whp_manufacturer.name,whp_user.name as created_by");
        $this->db->from("whp_stock_transaction_detail");
        $this->db->join("whp_stock_transaction","whp_stock_transaction_detail.stock_transaction_id=whp_stock_transaction.id","left");
        $this->db->join("whp_manufacturer","whp_stock_transaction.manufacturer_id=whp_manufacturer.id","left");
        $this->db->join("whp_user","whp_stock_transaction.user_id=whp_user.id","left");
        $this->db->where("whp_stock_transaction_detail.product_id",$id);
        $this->db->where("whp_stock_transaction_detail.del","0");
        $this->db->order_by("whp_stock_transaction_detail.date_created", "DESC");
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
		
		$this->db->select("id");
		$this->db->from("whp_stock_transaction_detail");
		$this->db->where("whp_stock_transaction_detail.product_id",$id);
		$this->db->where("del","0");
		$row=$this->db->get()->num_rows();
		
		$array=array("data"=>$result,"row"=>$row);
		
		return $array;
        
    }
    
    public function get_stock_shift(){
        if(!empty($this->input->get("search"))){
            $this->db->like('date', $this->input->get("search"));
        }
        // $query = $this->db->get("whp_products");
        $this->db->select("*");
        $this->db->join("whp_stock_transaction_detail","whp_stock_transaction.id=whp_stock_transaction_detail.stock_transaction_id","left");
        $this->db->from(" whp_stock_transaction");
        $this->db->where("whp_stock_transaction_detail.product_id",$_POST["product_id"]);
        $this->db->where("whp_stock_transaction.del","0");
        $this->db->order_by("whp_stock_transaction.id", "DESC");
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        return $result;
    }
    
    public function get_products(){
        if(!empty($this->input->get("search"))){
            $this->db->like('product_name', $this->input->get("search"));
            $this->db->or_like('brand', $this->input->get("search"));
            $this->db->or_like('category', $this->input->get("search"));
            $this->db->or_like('subcategory', $this->input->get("search"));
        }
        // $query = $this->db->get("whp_products");
        $this->db->select("*");
        $this->db->from("whp_products");
        $this->db->where("del","0");
        $this->db->order_by("whp_products.id", "DESC");
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        
        return $result;
    }

    public function get_stock_shift_()
    {
        // print_r($_POST);
        // exit;
        
        $this->db->select("whp_stock_shifting.id,whp_stock_shifting.status,whp_stock_shifting.slip_id,whp_stock_shifting.date,whp_user.name");
        $this->db->from("whp_stock_shifting");
        $this->db->join("whp_user","whp_stock_shifting.user_id=whp_user.id","left");
        $this->db->where("whp_stock_shifting.del","0");
        $this->db->order_by("whp_stock_shifting.id","DESC");
        
        if(isset($_POST["search"]["search_name"]))
        {
            $this->db->like("whp_user.name",$_POST["search"]["search_name"]);
        }
        
        $tmp_db=clone $this->db;
        $row=$tmp_db->get()->num_rows();
        
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $result=$this->db->get()->result_array();
        
        for($i=0;$i<count($result); $i++)
        {
            $this->db->select("whp_stock_shifting_detail.product_id,whp_stock_shifting_detail.swap_qty,whp_stock_shifting_detail.shop_qty,whp_stock_shifting_detail.warehouse_a_qty,whp_stock_shifting_detail.warehouse_b_qty,whp_stock_shifting_detail.type,whp_products.cat_no");
            $this->db->from("whp_stock_shifting_detail");
            $this->db->where("whp_stock_shifting_detail.shift_id",$result[$i]["id"]);
            $this->db->join("whp_products","whp_stock_shifting_detail.product_id=whp_products.id","left");
            $this->db->where("whp_stock_shifting_detail.del","0");
            $shift=$this->db->get()->result_array();
            $result[$i]["shift"]=$shift;
        }
        
        // $this->db->select("id");
        // $this->db->from("whp_stock_shifting");
        // $this->db->where("del","0");
        // $row=$this->db->get()->num_rows();
        
        $array=array("row"=>$row,"data"=>$result);
        
        return $array;
    }
    
    public function get_warehouse(){
      $query=$this->db->distinct()->select('warehouse_name')->where('del','0')->order_by("whp_stock_warehouse.warehouse_name", "ASC")->get('whp_stock_warehouse');
      return $query->result_array();
    }
   
}
 