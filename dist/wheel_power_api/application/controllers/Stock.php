<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class stock extends MY_Controller {


    public function __construct() {
        parent:: __construct();
        $this->load->model('stockModel');
        $this->load->database();
    }

    //To User Login

    // public function get_incoming_stock()
    // {
    //     $_POST=json_decode(file_get_contents("php://input"),true);
    //     $stock=new StockModel;
    //     $data=$stock->incoming_stock();
    //     echo json_encode($data);
    // }
    
    public function get()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        $stock=new StockModel;
        $data['data']=$stock->get_stock();
     
        if(sizeof($data)>0)
        { 
            $msg='Success';
        }
        else
        {
            $msg='Failed';
        }
        echo json_encode(array('data'=> $data,'msg'=>$msg));
    }
    
    public function get_ware_house()
    {
        $stock=new StockModel;
        $data=$stock->get_warehouse();
        if(sizeof($data)>0)
        { 
            $msg='Success';
        }
        else
        {
            $msg='Failed';
        }
        echo json_encode(array('data'=> $data,'msg'=>$msg));
    }
    
    public function get_stock_return()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        $stock=new StockModel;
        $data=$stock->get_all_stock_return();
        echo json_encode($data);
    }
   
    public function store()
    {
        if(isset($_POST['vendor_id']) && $_POST['vendor_id'])
        { $vendor_id = json_decode($_POST['vendor_id']); }
        else
        { $vendor_id = ""; }
        
        if(isset($_POST['invoice_no']) && $_POST['invoice_no'])
        { $invoice_no = json_decode($_POST['invoice_no']); }
        else
        { $invoice_no = ""; }
        
        if(isset($_POST['invoice_date']) && $_POST['invoice_date'])
        { $invoice_date = json_decode($_POST['invoice_date']); }
        else
        { $invoice_date = ""; }
        
        if(isset($_POST['invoice_amount']) && $_POST['invoice_amount'])
        { $invoice_amount = json_decode($_POST['invoice_amount']); }
        else
        { $invoice_amount = ""; }
        
        $data = array(
            'date_created' => date("Y-m-d H:i:sa"),
            'warehouse_id' => json_decode($_POST['user_type']),
            'vendor_id' => $vendor_id,
            'invoice_no' => $invoice_no,
            'invoice_date' =>  $invoice_date,
            'invoice_amount' =>  $invoice_amount,
            'del' => '0',
            
        );
        
     
        $data=$this->db->insert('whp_receive_item', $data);
        
        $lid = $this->db->insert_id();
        
        if(isset($_FILES['image']['name']))
        {
            if($_FILES['image']['name']!='')
            {
                $img_nam=$this->image_upload($_FILES['image'],$lid);
                if($img_nam!='')
                {
                    $this->db->where('whp_receive_item.id', $lid);
                    $dataupdate=$this->db->update('whp_receive_item',array('image'=>$img_nam));
                }
            }
        }
        
        $this->insertitem_detail(json_decode($_POST['product_arr']),$lid,json_decode($_POST['user_type']));
       
        echo json_decode($data);
    }
    
    
    public function image_upload($files,$id)
    {
        $type=$files["type"];
        $name=$files["name"];
      	$uploaded_path = $_SERVER['DOCUMENT_ROOT'].'/wheel_power_new/wheel_power_api/uploads'. DIRECTORY_SEPARATOR .'invoices'. DIRECTORY_SEPARATOR ;
	    $tmp_name=$files["tmp_name"];
		$img_name="invoice_of_stock_id_".$id."_".uniqid();
		$tmp_ext=explode(".",$name);
		$ext=strtolower(end($tmp_ext));
		$uploaded_path.=$img_name;
		$uploaded_path.=".".$ext;
    	if(move_uploaded_file($tmp_name, $uploaded_path)){
		   return $img_name.".".$ext;
		}
    }
    
    public function insertitem_detail($detail,$id,$warehouse_id)
    {
        foreach ($detail as $pdtl)
        {
            // print_r($pdtl);
            $item_data = array(
                'date_created' => date("Y-m-d H:i:sa"),
                'receive_item_id' => $id,
                'product_id' =>$pdtl->product_id,
                'brand' =>$pdtl->brand,
                'category' =>$pdtl->category,
                'sub_category' =>$pdtl->sub_category,
                'product_name' =>$pdtl->name,
                'cat_no' =>$pdtl->cat_no,
                'price' =>$pdtl->price,
                'qty' => (int)$pdtl->qty,
                'del' => '0',
            );
            $data1=$this->db->insert('whp_receive_item_detail', $item_data);
          
            $product_exist = $this->db->select("whp_warehouse_product.current_stock,whp_warehouse_product.id")->from('whp_warehouse_product')->where('whp_warehouse_product.warehouse_id',$warehouse_id)->where('whp_warehouse_product.cat_no',$pdtl->cat_no)->where('whp_warehouse_product.del','0')->get()->row_array();
            
            // print_r($product_exist);
            if(isset($product_exist['current_stock']))
            {
                // echo "update";
                $update_array = array(
                    'current_stock' => $product_exist['current_stock']+(int)$pdtl->qty,
                    );
                $this->db->where("whp_warehouse_product.id",$product_exist['id']);
                $this->db->update('whp_warehouse_product',$update_array);
            }
            else
            {
                // echo "insert";
                $warehouse_data = array(
                    'date_created' => date("Y-m-d H:i:sa"),
                    'warehouse_id' => $warehouse_id,
                    'brand' =>$pdtl->brand,
                    'category' =>$pdtl->category,
                    'sub_category' =>$pdtl->sub_category,
                    'product_name' =>$pdtl->name,
                    'cat_no' =>$pdtl->cat_no,
                    'product_name' =>$pdtl->name,
                    'price' =>$pdtl->price,
                    'current_stock' => (int)$pdtl->qty,
                    'del' => '0',
                );
                
                $this->db->insert('whp_warehouse_product', $warehouse_data);
            }
            
           
            
            if($data1){
                //product table updated
                // $this->product_stock_update($pdtl->product_id,(int)$pdtl->sh_qty,((int)$pdtl->wr_qty1+(int)$pdtl->wr_qty2));
                //now warehouse should be updated
                
                //stock add
                // if((int)$pdtl->wr_qty1 > 0 && $type=="add"){
                //     $this->update_warehouse("Warehouse A",$pdtl->product_id,(int)$pdtl->wr_qty1);
                // }
                // if((int)$pdtl->wr_qty2 > 0 && $type=="add"){
                //     $this->update_warehouse("Warehouse B",$pdtl->product_id,(int)$pdtl->wr_qty2);
                // }
                
                //stock return
                // if((int)$pdtl->wr_qty1 < 0 && $type=="return"){
                //     $this->update_warehouse("Warehouse A",$pdtl->product_id,(int)$pdtl->wr_qty1);
                // }
                // if((int)$pdtl->wr_qty2 < 0 && $type=="return"){
                //     $this->update_warehouse("Warehouse B",$pdtl->product_id,(int)$pdtl->wr_qty2);
                // }
            }
        }
    }
    
    public function product_stock_update($p_id,$sq,$wq){
        // echo "<br>".$p_id."<br>".$sq."<br>".$wq."<br>";
        
        $product = $this->db->select("whp_products.shop_qty,whp_products.warehouse_qty");
        $product = $this->db->from("whp_products");
        $product = $this->db->where('whp_products.id',$p_id);
        $product = $this->db->where('whp_products.del','0');
        $product = $this->db->get()->row_array(); 
        //print_r($product);
        $product['shop_qty']=$product['shop_qty']+$sq;
        $product['warehouse_qty']=$product['warehouse_qty']+$wq;
        $this->db->where('whp_products.id', $p_id);
        $data=$this->db->update('whp_products',$product);
        return $data;
    }
  
  
    public function update_warehouse($wh,$pid,$qty){
        //if product id exist ware house table then update else insert
        $this->db->select("whp_stock_warehouse.id,whp_stock_warehouse.qty,whp_stock_warehouse.warehouse_name,whp_stock_warehouse.product_id");
        $this->db->from("whp_stock_warehouse");
        $this->db->where('whp_stock_warehouse.product_id',$pid);
        $this->db->where('whp_stock_warehouse.del','0');
        $this->db->where('whp_stock_warehouse.warehouse_name',$wh);
        $count = $this->db->count_all_results();
        // $count;
        if($count>0){
            $this->db->select("whp_stock_warehouse.id,whp_stock_warehouse.qty,whp_stock_warehouse.warehouse_name,whp_stock_warehouse.product_id");
            $this->db->from("whp_stock_warehouse");
            $this->db->where('whp_stock_warehouse.product_id',$pid);
            $this->db->where('whp_stock_warehouse.del','0');
            $this->db->where('whp_stock_warehouse.warehouse_name',$wh);
            $warehouse = $this->db->get()->row_array();
            if(isset($warehouse['qty'])){
                $warehouse['qty']=$warehouse['qty']+$qty;
                $this->db->where('whp_stock_warehouse.id', $warehouse['id']);
                $this->db->update('whp_stock_warehouse',$warehouse); 
            }
        }else{
             $data = array(
                'product_id' => $pid,
                'warehouse_name' => $wh,
                'qty' =>  $qty,
                'del' => '0'
             );
            $data=$this->db->insert('whp_stock_warehouse', $data);
        }
        
    }
    
    /**
     * Edit Data from this method.
     *
     * @return Response
     */
    
    
    public function edit($id)
    {
        $stock = $this->db->get_where('whp_stock', array('id' => $id))->row();
        return $stock;
    }
    /**
     * Update Data from this method.
     *
     * @return Response
     */
    public function update($id)
    {
        $stock=new StockModel;
        $stock->update_stock($id);
        return $stock;
    }
    /**
     * Delete Data from this method.
     *
     * @return Response
     */

    public function delete_shift(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        //print_r($_POST);
        $data['del']='1';
        $this->db->where('whp_stock_shifting.id', $_POST);
        $this->db->where('whp_stock_shifting.status', 'Pending');
        $del=$this->db->update('whp_stock_shifting',$data);
        if($del){
            $data1['del']='1';
            $this->db->where('whp_stock_shifting_detail.shift_id', $_POST);
            $del1=$this->db->update('whp_stock_shifting_detail',$data1);
        }
        if($del)
        { 
            $msg='Success';
        }
        else
        {
            $msg='Failed';
        }
        echo json_encode(array('msg'=>$msg));
    }

    public function stock_shift_get()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $stock=new StockModel;
        $data=$stock->get_stock_shift_();
        // print_r($data);
        echo json_encode($data);
    }
//    public function create()
//    {
//       // $this->load->view('includes/header');
//        $this->load->view('products/create');
//       // $this->load->view('includes/footer');
//    }
   
    public function stock_shift_store()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $lid=0;
        $data = array(
            'date' =>  $_POST[0]['invoice_date'],
            'user_id' =>  $_POST[0]['user_type'],
            'status' => 'Pending',
            'slip_id' => $_POST[0]["slip_id"],
            'del' => '0',
            'date_created' =>date('Y-m-d H:i:sa')
        );
        $dataf=$this->db->insert('whp_stock_shifting',$data);
        $lid = $this->db->insert_id();
        $this->insert_shift_detail($_POST,$lid);
        echo json_encode($lid);
        // $stock=new StockModel;
        // $data=$stock->insert_stock_shift();
        // echo json_decode($data);
    }
    
    public function insert_shift_detail($shift,$id){
        foreach ($shift as $pdtl){
              $swap=0;
              $type='';
              $status="done";
              if($pdtl['transaction'] == 'b_to_a')
              {    
                  $type="From Warehouse B To A";
                  $swap=(int)$pdtl['prev_warehouse_a_qty']-(int)$pdtl['warehouse_a'];
              }
              
              if($pdtl['transaction'] == 'a_to_b')
              { 
                  $type="From Warehouse A To B";
                  $swap=(int)$pdtl['prev_warehouse_a_qty']-(int)$pdtl['warehouse_a'];
              }
               
              if($pdtl['transaction'] == 'a_to_shop')
              { 
                  $type="From Warehouse A To Shop";
                  $swap=$pdtl['prev_shop_qty']-$pdtl['shop_qty'];
              }
              
              if($pdtl['transaction'] == 'shop_to_a')
              { 
                  $type="From Shop To Warehouse A"; 
                  $swap=$pdtl['prev_shop_qty']-$pdtl['shop_qty'];
              }
              
              if($pdtl['transaction'] == 'b_to_shop')
              { 
                  $type="From Warehouse B To Shop";
                  $swap=$pdtl['prev_shop_qty']-$pdtl['shop_qty'];
              }
              
              if($pdtl['transaction'] == 'shop_to_b')
              { 
                  $type="From Shop To Warehouse B"; 
                  $swap=$pdtl['prev_shop_qty']-$pdtl['shop_qty'];
              }
             
            //  print_r($pdtl);
                $data = array(
                    'shift_id' => $id,
                    'product_id' => $pdtl['id'],
                    'prev_warehouse_a_qty' => $pdtl['prev_warehouse_a_qty'],
                    'prev_warehouse_b_qty' => $pdtl['prev_warehouse_b_qty'],
                    'prev_shop_qty' => $pdtl['prev_warehouse_b_qty'],
                    'warehouse_a_qty' => $pdtl['warehouse_a'],
                    'warehouse_b_qty' => $pdtl['warehouse_b'],
                    'shop_qty' => $pdtl['shop_qty'],
                    'total_qty' => $pdtl['shop_qty']+$pdtl['warehouse_a']+$pdtl['warehouse_b'],
                    'swap_qty' => abs($swap),
                    'type' => $type,
                    'del' => '0',
                    'date_created' => date("Y-m-d H:i:sa")
               );
            $data=$this->db->insert('whp_stock_shifting_detail', $data);
            // if($data)
            // {
            //$product['shop_qty']=$pdtl['shop_qty'];
            //$product['warehouse_qty']=$pdtl['warehouse_a']+$pdtl['warehouse_b'];
            //$product["product_id"]=$pdtl['id'];
            //$this->update_stocks($product,$pdtl['id'],$status);
            //$data=array(); 
            // }
            $type='';
      
        }
    }
    
    public function update_stocks()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        //print_r($_POST);
        $stock = $this->db->select('whp_stock_shifting_detail.*')->from('whp_stock_shifting_detail')->where('whp_stock_shifting_detail.shift_id',$_POST)->get()->result_array();
       
        //print_r($stock);
        
        foreach($stock as $st){
              
               $qty=$st['swap_qty'];
              
              if($st['type'] == 'From Warehouse B To A')
              {  
                // echo $st['type'];
                // echo $qty=$st['swap_qty'];
                // echo $st['product_id'];
                 
                 $wh="Warehouse A";
                 $this->update_warehouse($wh,$st['product_id'],$qty);
                 $wh="Warehouse B";
                 $qty=-1*$qty;
                 $this->update_warehouse($wh,$st['product_id'],$qty);
              }
              
              if($st['type']  == 'From Warehouse A To B')
              { 
            //   echo $st['type'];
            //   echo $qty=$st['swap_qty'];
            //   echo $st['product_id'];
                  
                 $wh="Warehouse B";
                 $this->update_warehouse($wh,$st['product_id'],$qty);
                 $wh="Warehouse A";
                 $qty=-1*$qty;
                 $this->update_warehouse($wh,$st['product_id'],$qty);
              }
               
              if($st['type'] == 'From Warehouse A To Shop')
              { 
            //       echo $st['type'];
            //   echo $qty=$st['swap_qty'];
            //   echo $st['product_id'];
                $sq=$qty;
                $this->product_stock_update($st['product_id'],$sq,-1*$sq);
                $wh="Warehouse A";
                $qty=-1*$qty;
                $this->update_warehouse($wh,$st['product_id'],$qty);
              }
              
              if($st['type'] == 'From Shop To Warehouse A')
              { 
            //       echo $st['type'];
            //   echo $qty=$st['swap_qty'];
            //   echo $st['product_id'];
                $sq=-1*$qty;
                $this->product_stock_update($st['product_id'],$sq,$qty);
                $wh="Warehouse A";
                $this->update_warehouse($wh,$st['product_id'],$qty);
              }
              
              if($st['type'] == 'From Warehouse B To Shop')
              { 
            //       echo $st['type'];
            //   echo $qty=$st['swap_qty'];
            //   echo $st['product_id'];
                $sq=$qty;
                $this->product_stock_update($st['product_id'],$sq,-1*$sq);
                $wh="Warehouse B";
                $qty=-1*$qty;
                $this->update_warehouse($wh,$st['product_id'],$qty);
              }
              
              if($st['type'] == 'From Shop To Warehouse B')
              { 
                $sq=-1*$qty;
                $this->product_stock_update($st['product_id'],$sq,$qty);
                $wh="Warehouse B";
                $this->update_warehouse($wh,$st['product_id'],$qty);
              }
        }
        $this->db->query("update whp_stock_shifting set status='Done' where id='".$_POST."'");
        echo json_encode("success");
    }
    
    public function stock_shift_edit($id)
    {
        $stock = $this->db->get_where('whp_stock', array('id' => $id))->row();
        return $stock;
    }
   
   
////////////////////////////////////////////////////////////////////    MY CODE  ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    
    public function get_incoming_stock()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $cat_no = $this->decrypt($data['enct_cat']);
        
        $this->db->select("whp_products.id as product_id,whp_products.cat_no");
        $this->db->from("whp_products");
        $this->db->where("whp_products.cat_no",$cat_no);
        $this->db->where("whp_products.del",'0');
        $product = $this->db->get()->row_array();
      
        $this->db->select("whp_receive_item.*,whp_manufacturer.name as vendor_name, COUNT(whp_receive_item.vendor_id) as item");
        $this->db->from("whp_receive_item_detail");
        $this->db->join("whp_receive_item","whp_receive_item.id = whp_receive_item_detail.receive_item_id","left");
        $this->db->where("whp_receive_item_detail.product_id",$product['product_id']);
        $this->db->where("whp_receive_item.warehouse_id",$data['warehouse_id']);
        $this->db->join("whp_manufacturer","whp_manufacturer.id = whp_receive_item.vendor_id","left");
        $this->db->group_by("whp_receive_item.vendor_id");
        $this->db->where("whp_receive_item_detail.del","0");
        $item = $this->db->get()->result_array();
        
        $i=0;
        foreach($item as $val)
        {
            $item[$i]['product_id'] = $product['product_id'];
            $i++;
        }
        
        echo json_encode(array("incoming_list" => array_filter($item)));
    }
    
    
    public function incoming_stock_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("whp_receive_item.date_created,whp_receive_item_detail.qty");
        $this->db->from("whp_receive_item");
        $this->db->where("whp_receive_item.warehouse_id",$data['warehouse_id']);
        $this->db->where("whp_receive_item.vendor_id",$data['vendor_id']);
        $this->db->join("whp_receive_item_detail","whp_receive_item_detail.receive_item_id = whp_receive_item.id AND whp_receive_item_detail.product_id = '".$data['product_id']."' ");
        $this->db->where("whp_receive_item.del","0");
        $detail = $this->db->get()->result_array();
        
        echo json_encode(array('incoming_detail' => $detail));
    }


    public function get_transfer_stock()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $cat_no = $this->decrypt($data['enct_cat']);
        
        $sel_str="";
        if($data['search_type'] == 'in')
        {
            $sel_str = "COUNT(whp_stock_transfer.warehouse_to) as total_item";
        }
        else
        {
            $sel_str = "COUNT(whp_stock_transfer.warehouse_from) as total_item";
        }
        
        $this->db->select("whp_stock_transfer_detail.*,whp_warehouse.warehouse_name,whp_warehouse.id as warehouse_id,".$sel_str." ");
        $this->db->from("whp_stock_transfer_detail");
        $this->db->join("whp_stock_transfer","whp_stock_transfer.id = whp_stock_transfer_detail.stock_transfer_id","left");
        $this->db->where("whp_stock_transfer_detail.cat_no",$cat_no);
        if($data['search_type'] == 'in')
        {
            $this->db->where("whp_stock_transfer.warehouse_to",$data['warehouse_id']);
            $this->db->join("whp_warehouse","whp_warehouse.id = whp_stock_transfer.warehouse_from","left");   
            $this->db->group_by("whp_stock_transfer.warehouse_from");
        }
        else
        {
            $this->db->where("whp_stock_transfer.warehouse_from",$data['warehouse_id']);
            $this->db->join("whp_warehouse","whp_warehouse.id = whp_stock_transfer.warehouse_to","left");
            $this->db->group_by("whp_stock_transfer.warehouse_to");
        }
        $this->db->where("whp_stock_transfer_detail.del","0");
        $item = $this->db->get()->result_array();
        
        echo json_encode(array("stock_transfer" => $item));
    }
    
    public function stock_transfer_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        // print_r($data);
        
        $this->db->select("whp_stock_transfer.date_created,whp_stock_transfer_detail.qty,whp_stock_transfer_detail.cat_no");
        $this->db->from("whp_stock_transfer");
        if($data['page_type'] == 'in')
        {
            $this->db->where("whp_stock_transfer.warehouse_from",$data['warehouse_id']);
        }
        else
        {
            $this->db->where("whp_stock_transfer.warehouse_to",$data['warehouse_id']);
        }
        $this->db->join("whp_stock_transfer_detail","whp_stock_transfer_detail.stock_transfer_id = whp_stock_transfer.id AND whp_stock_transfer_detail.cat_no = '".$data['cat_no']."' ");
        $this->db->where("whp_stock_transfer.del","0");
        $detail = $this->db->get()->result_array();
        
        echo json_encode(array('transfer_detail' => $detail));
    }

    public function submit_stock_transfer()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        $date_created = date("Y-m-d h:i:s");
     
        $total_qty = 0;
        $array = array(
            'date_created' => $date_created,
            'warehouse_from' => $data['warehouse_from'],
            'warehouse_to' => $data['warehouse_to'],
            'total_qty' => $data['total_qty'],
            'status' => 'pending'
            );
            $last_id = 0;
        $this->db->insert("whp_stock_transfer",$array);
        
        $last_id = $this->db->insert_id();
        
        foreach($data['product_data'] as $val)
        {
            $submit=array(
                
            'date_created' => $date_created,
            'stock_transfer_id' => $last_id,
            'brand' => $val['brand'],
            'category' => $val['category'],
            'sub_category' => $val['sub_category'],
            'product_name' => $val['name'],
            'cat_no' => $val['cat_no'],
            'price' => $val['price'],
            'qty' => $val['qty'],
            );
            
            $this->db->insert("whp_stock_transfer_detail",$submit);
            
        }
        
        echo json_encode("success");
        
    }

    

    
    public function stock_transfer_list()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        
        $this->db->select("whp_stock_transfer.*,whp_warehouse.warehouse_name as warehouse_fr_name,tmp_warehouse.warehouse_name as warehouse_to_name,whp_warehouse.id as warehouse_id");
        $this->db->from("whp_stock_transfer");
        $this->db->where("whp_stock_transfer.warehouse_to",$data['warehouse_id']);
        $this->db->join("whp_warehouse","whp_warehouse.id = whp_stock_transfer.warehouse_from","left");
        $this->db->join("whp_warehouse as tmp_warehouse","tmp_warehouse.id = whp_stock_transfer.warehouse_to","left");
        if(isset($data['search']['transfer_type']) && $data['search']['transfer_type'])
        {
            $this->db->where("whp_stock_transfer.status",$data['search']['transfer_type']);
        }
        // $this->db->group_by("whp_stock_transfer.warehouse_from");
        $tmp_db = clone $this->db;
        $this->db->limit($data['pagelimit'],$data['start']);
        $this->db->where("whp_stock_transfer.del","0");
        $item = $this->db->get()->result_array();
        
        foreach ($item as $i => $v)
		{
			$item[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        
        
        $total_row = $tmp_db->get()->num_rows();
        
        for($i=0; $i<count($item); $i++)
        {
            $this->db->select("*");
            $this->db->from("whp_stock_transfer_detail");
            $this->db->where("stock_transfer_id",$item[$i]['id']);
            $this->db->where("del","0");
            $detail = $this->db->get()->result_array();
            
            $item[$i]['item_detail'] = $detail;
        }
        
        echo json_encode(array("data" => $item, "row"=>$total_row));
    }
    
    
    public function get_transfer_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $id = $this->decrypt($data);
        
        $this->db->select("whp_stock_transfer_detail.*,whp_stock_transfer.warehouse_from,whp_stock_transfer.warehouse_to");
        $this->db->from("whp_stock_transfer_detail");
        $this->db->where("whp_stock_transfer_detail.stock_transfer_id",$id);
        $this->db->join("whp_stock_transfer","whp_stock_transfer.id = whp_stock_transfer_detail.stock_transfer_id","left");
        $this->db->where("whp_stock_transfer_detail.del","0");
        $item_detail = $this->db->get()->result_array();
        
        echo json_encode(array("item_detail" => $item_detail));
    }
    
    public function transfer_action()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $date_created = date("Y-m-d h:i:s");
   
        foreach($data as $val)
        {
            $item_id_from = $this->db->select("whp_warehouse_product.id,whp_warehouse_product.current_stock")->from('whp_warehouse_product')->where("whp_warehouse_product.cat_no",$val['cat_no'])->where("whp_warehouse_product.del",'0')->where("whp_warehouse_product.warehouse_id",$val['warehouse_from'])->get()->row_array();
            
            $cal_stock=0;
            if($item_id_from)
            {
                $cal_stock = (int)($item_id_from['current_stock']) - (int)($val['accept_qty']);
                $this->db->query("update whp_warehouse_product set current_stock='".$cal_stock."' where id='".$item_id_from['id']."' ");
            }
            
            $item_id_to = $this->db->select("whp_warehouse_product.id,whp_warehouse_product.current_stock")->from('whp_warehouse_product')->where("whp_warehouse_product.cat_no",$val['cat_no'])->where("whp_warehouse_product.del",'0')->where("whp_warehouse_product.warehouse_id",$val['warehouse_to'])->get()->row_array();
            
            $cal_stock1=0;
            if($item_id_to)
            {
                $cal_stock1 = (int)($item_id_to['current_stock']) + (int)($val['accept_qty']);
                $this->db->query("update whp_warehouse_product set current_stock='".$cal_stock1."' where id='".$item_id_to['id']."' ");
            }
            else
            {
                $product=array(
                
                'date_created' => $date_created,
                'warehouse_id' => $val['warehouse_to'],
                'brand' => $val['brand'],
                'category' => $val['category'],
                'sub_category' => $val['sub_category'],
                'product_name' => $val['product_name'],
                'cat_no' => $val['cat_no'],
                'price' => $val['price'],
                'current_stock' => $val['accept_qty'],
                );
                
                $this->db->insert("whp_warehouse_product",$product);
            }
        }
        
        $this->db->query("update whp_stock_transfer set status = 'received' where id = '".$data[0]['stock_transfer_id']."'");
        
        echo json_encode("success");
    }

    public function submit_stock_return()
    {
        $data = array(
        'date_created' => date("Y-m-d H:i:sa"),
        'warehouse_id' => json_decode($_POST['warehouse_id']),
        'vendor_id' => json_decode($_POST['vendor']),
        'del' => '0',
        );
        
     
        $data=$this->db->insert('whp_stock_return', $data);
        
        $lid = $this->db->insert_id();
        
        if(isset($_FILES['image']['name']))
        {
            if($_FILES['image']['name']!='')
            {
                $img_nam=$this->image_upload($_FILES['image'],$lid);
                if($img_nam!='')
                {
                    $this->db->where('whp_stock_return.id', $lid);
                    $dataupdate=$this->db->update('whp_stock_return',array('image'=>$img_nam));
                }
            }
        }
        // $lid=0;
        $this->insertstock_detail(json_decode($_POST['product_arr']),$lid,json_decode($_POST['warehouse_id']));
       
        echo json_decode($data);
    }
    
    public function insertstock_detail($detail,$id,$warehouse_id)
    {
        foreach ($detail as $pdtl)
        {
            // print_r($pdtl);
            $item_data = array(
                'date_created' => date("Y-m-d H:i:sa"),
                'stock_return_id' => $id,
                'brand' =>$pdtl->brand,
                'category' =>$pdtl->category,
                'sub_category' =>$pdtl->sub_category,
                'product_name' =>$pdtl->name,
                'cat_no' =>$pdtl->cat_no,
                'price' =>$pdtl->price,
                'qty' => (int)$pdtl->qty,
                'del' => '0',
            );
            $data1=$this->db->insert('whp_stock_return_detail', $item_data);
          
            $product_exist = $this->db->select("whp_warehouse_product.current_stock,whp_warehouse_product.id")->from('whp_warehouse_product')->where('whp_warehouse_product.warehouse_id',$warehouse_id)->where('whp_warehouse_product.cat_no',$pdtl->cat_no)->where('whp_warehouse_product.del','0')->get()->row_array();
            
            // print_r($product_exist);
            
            if(isset($product_exist['current_stock']) && $product_exist['current_stock'])
            {
                $update_array = array(
                    'current_stock' => $product_exist['current_stock']-(int)$pdtl->qty,
                    );
                $this->db->where("whp_warehouse_product.id",$product_exist['id']);
                $this->db->update('whp_warehouse_product',$update_array);
            }
          
        }
    }
    
    public function get_return_stock()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $cat_no = $this->decrypt($data['enct_cat']);
        
        $this->db->select("whp_stock_return.*,whp_stock_return_detail.cat_no,whp_manufacturer.name as vendor_name, COUNT(whp_stock_return.vendor_id) as item");
        $this->db->from("whp_stock_return_detail");
        $this->db->join("whp_stock_return","whp_stock_return.id = whp_stock_return_detail.stock_return_id","left");
        $this->db->where("whp_stock_return_detail.cat_no",$cat_no);
        $this->db->where("whp_stock_return.warehouse_id",$data['warehouse_id']);
        $this->db->join("whp_manufacturer","whp_manufacturer.id = whp_stock_return.vendor_id","left");
        $this->db->group_by("whp_stock_return.vendor_id");
        $this->db->where("whp_stock_return_detail.del","0");
        $item = $this->db->get()->result_array();
       
        echo json_encode(array("return_list" => array_filter($item)));
    }
    
    public function stock_return_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("whp_stock_return.date_created,whp_stock_return_detail.qty");
        $this->db->from("whp_stock_return");
        $this->db->where("whp_stock_return.warehouse_id",$data['warehouse_id']);
        $this->db->where("whp_stock_return.vendor_id",$data['vendor_id']);
        $this->db->join("whp_stock_return_detail","whp_stock_return_detail.stock_return_id = whp_stock_return.id AND whp_stock_return_detail.cat_no = '".$data['cat_no']."' ");
        $this->db->where("whp_stock_return.del","0");
        $detail = $this->db->get()->result_array();
        
        echo json_encode(array('return_detail' => $detail));
    }
    
    public function update_stock()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        for($i=0; $i<count($data['stock']); $i++)    
        {
            $date_created = date("Y-m-d h:i:s");
            
            $result = $this->db->select("id")->from("whp_warehouse_product")->where("warehouse_id",$data['warehouse_id'])->where("cat_no",$data['stock'][$i]['cat_no'])->where('del','0')->get()->row_array();
            
            if(isset($result['id']) && $result['id'])
            {
                $array = array(
                'current_stock' => $data['stock'][$i]['current_stock'],
                );
                
                $this->db->where("warehouse_id",$data['warehouse_id']);
                $this->db->where("id",$result['id']);
                $this->db->update("whp_warehouse_product",$array);
            }
            else
            {
                $product=array(
                
                'date_created' => $date_created,
                'warehouse_id' => $data['warehouse_id'],
                'brand' => $data['stock'][$i]['brand'],
                'category' => $data['stock'][$i]['category'],
                'sub_category' => $data['stock'][$i]['sub_category'],
                'product_name' => $data['stock'][$i]['name'],
                'cat_no' => $data['stock'][$i]['cat_no'],
                'price' => $data['stock'][$i]['price'],
                'current_stock' => $data['stock'][$i]['current_stock'],
                );
                
                $this->db->insert("whp_warehouse_product",$product);
            }
        
            
            $msg = "success";
        }
        
        echo json_encode($msg);
        
    }

}
?>
