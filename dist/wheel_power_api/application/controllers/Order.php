<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class order extends MY_Controller {
    
    
    public function __construct() {
      parent:: __construct();
      $this->load->model('productModel');
      $this->load->database();
    }
 
    public function order_list()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        if($data['page_type'] == 'pending' || $data['page_type'] == 'dispatch')
        {
            $this->db->select("whp_order.date_created as order_date,whp_order.user_id as order_user_id,whp_order.status,whp_order.id as order_id,whp_customer.*,whp_warehouse.warehouse_name,whp_order.user_type");
            $this->db->from("whp_order");
            $this->db->join("whp_customer","whp_order.customer_id = whp_customer.id","left");
            $this->db->join("whp_warehouse","whp_order.assign_warehouse = whp_warehouse.id","left");
            if(isset($data['search']['customer']) && $data['search']['customer']!='')
            {
                $this->db->group_start();
                $this->db->like("whp_customer.name",$data['search']['customer']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['customer']);
                $this->db->group_end();
            }
            if($data['page_type'] == 'dispatch')
            {
                $this->db->where("whp_order.status","deliver");
            }
            else
            {
                $this->db->where("whp_order.status","pending");
            }
            // $this->db->join("whp_user","whp_order.user_id = whp_user.id","left");
            $this->db->order_by("whp_order.id","desc");
            $this->db->where("whp_order.del","0");
            $order_list = $this->db->get()->result_array();
            
            foreach ($order_list as $i => $v)
    		{
                if($v['user_type'] == "customer")
                {
                    $customer = $this->db->select("name")->from("whp_customer")->where("del","0")->where("id",$v["order_user_id"])->get()->row_array();
                    $order_list[$i]['created_by_name'] = $customer['name'];
                }
                else
                {
                    $user = $this->db->select("name")->from("whp_user")->where("del","0")->where("id",$v["order_user_id"])->get()->row_array();
                    $order_list[$i]['created_by_name'] = $user['name'];
                }

    		    $total_item = $this->db->select("id")->from("whp_order_item")->where("del","0")->where("order_id",$v["order_id"])->get()->num_rows();
    		    
    		    $order_list[$i]['total_item'] =  $total_item;
    			$order_list[$i]['ecrpt_id'] =  $this->encrypt($v["order_id"]);
    		}
        }
        
        else if($data['page_type'] == 'assign_to_warehouse')
        {
            $this->db->select("whp_order_packing.order_id");
            $this->db->from("whp_order_packing");
            $this->db->where("whp_order_packing.del","0");
            $this->db->group_by("whp_order_packing.order_id");
            $packing = $this->db->get()->result_array();
            
            $this->db->select("whp_order.date_created as order_date,whp_order.user_type,whp_order.status,whp_order.id as order_id,whp_order.user_id as order_user_id,whp_customer.*,whp_warehouse.warehouse_name");
            $this->db->from("whp_order");
            $this->db->join("whp_customer","whp_order.customer_id = whp_customer.id","left");
            if(isset($data['search']['customer']) && $data['search']['customer']!='')
            {
                $this->db->group_start();
                $this->db->like("whp_customer.name",$data['search']['customer']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['customer']);
                $this->db->group_end();
            }
            $this->db->join("whp_warehouse","whp_order.assign_warehouse = whp_warehouse.id","left");
            $this->db->join("whp_user","whp_order.user_id = whp_user.id","left");
            $this->db->where("whp_order.status","assign");
            $this->db->order_by("whp_order.id","desc");
            if($packing)
            {
                foreach($packing as $data)
                {
                    $this->db->where("whp_order.id !=",$data['order_id']);
                }
            }
            $this->db->where("whp_order.del","0");
            $order_list = $this->db->get()->result_array();
            
            foreach ($order_list as $i => $v)
    		{
                if($v['user_type'] == "customer")
                {
                    $customer = $this->db->select("name")->from("whp_customer")->where("del","0")->where("id",$v["order_user_id"])->get()->row_array();
                    $order_list[$i]['created_by_name'] = $customer['name'];
                }
                else
                {
                    $user = $this->db->select("name")->from("whp_user")->where("del","0")->where("id",$v["order_user_id"])->get()->row_array();
                    $order_list[$i]['created_by_name'] = $user['name'];
                }

    		    $total_item = $this->db->select("id")->from("whp_order_item")->where("del","0")->where("order_id",$v["order_id"])->get()->num_rows();
    		    
    		    $order_list[$i]['total_item'] =  $total_item;
    			$order_list[$i]['ecrpt_id'] =  $this->encrypt($v["order_id"]);
    		}
        }
        else
        {
            $this->db->select("whp_order_packing.date_created as order_date,whp_order_packing.order_id as order_id,whp_order_packing.id as packing_id,whp_order_packing.item,whp_customer.*");
            $this->db->from("whp_order_packing");
            $this->db->join("whp_customer","whp_order_packing.customer_id = whp_customer.id","left");
            $this->db->where("whp_order_packing.del","0");
            if(isset($data['search']['customer']) && $data['search']['customer']!='')
            {
                $this->db->group_start();
                $this->db->like("whp_customer.name",$data['search']['customer']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['customer']);
                $this->db->group_end();
            }
            if($data['page_type'] == 'invoice')
            {
                $this->db->where("whp_order_packing.status","pending");
                $this->db->where("whp_order_packing.invoice_no !='' ");
            }
            if($data['page_type'] == 'packing')
            {
                $this->db->where("whp_order_packing.status","pending");
                $this->db->where("whp_order_packing.invoice_no =''");
            }
            
            $this->db->order_by("whp_order_packing.id","desc");
            
            if(isset($data['pagelimit']) && $data['pagelimit'])
            {
                $this->db->limit($data['pagelimit'],$data['start']);
            }
            $order_list = $this->db->get()->result_array();
            
            foreach ($order_list as $i => $v)
    		{
    			// $order_data = $this->db->select("id")->from("whp_order_item")->where("del","0")->where("order_id",$v["order_id"])->get()->row_array();

    			$this->db->select("whp_order.user_id,whp_order.user_type");
		        $this->db->from("whp_order");
		        $this->db->where("whp_order.id",$v['order_id']);
		        $this->db->where("whp_order.del","0");
		        $order_data = $this->db->get()->row_array();

		        if($order_data['user_type'] == 'customer')
		        {
		        	$created_by = $this->db->select("name")->from("whp_customer")->where("del","0")->where("id",$order_data["user_id"])->get()->row_array();
		        }
		        else
		        {
		        	$created_by = $this->db->select("name")->from("whp_user")->where("del","0")->where("id",$order_data["user_id"])->get()->row_array();
		        }

    		    $total_item = $this->db->select("id")->from("whp_order_item")->where("del","0")->where("order_id",$v["order_id"])->get()->num_rows();
    		    
    		    $order_list[$i]['created_by_name'] =  $created_by['name'];
    		    $order_list[$i]['total_item'] =  $total_item;
    			$order_list[$i]['ecrpt_id'] =  $this->encrypt($v["packing_id"]);
    			$order_list[$i]['ecrpt_order_id'] =  $this->encrypt($v["order_id"]);
    		}
        }
        
        $this->db->select("whp_order.id");
        $this->db->from("whp_order");
        $this->db->where("whp_order.status","pending");
        $this->db->where("whp_order.del","0");
        $pending_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_order_packing.order_id");
        $this->db->from("whp_order_packing");
        $this->db->where("whp_order_packing.del","0");
        $this->db->group_by("whp_order_packing.order_id");
        $packing = $this->db->get()->result_array();
        
        $this->db->select("whp_order.id");
        $this->db->from("whp_order");
        $this->db->where("whp_order.status","assign");
        if($packing)
        {
            foreach($packing as $data)
            {
                $this->db->where("whp_order.id !=",$data['order_id']);
            }
        }
        $this->db->where("whp_order.del","0");
        $assign_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_order_packing.id");
        $this->db->from("whp_order_packing");
        $this->db->where("whp_order_packing.status","pending");
        $this->db->where("whp_order_packing.invoice_no !='' ");
        $this->db->where("whp_order_packing.del","0");
        $invoice_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_order_packing.id");
        $this->db->from("whp_order_packing");
        $this->db->where("whp_order_packing.status","pending");
        $this->db->where("whp_order_packing.invoice_no ='' ");
        $this->db->where("whp_order_packing.del","0");
        $packing_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_order.id");
        $this->db->from("whp_order");
        $this->db->where("whp_order.status","deliver");
        $this->db->where("whp_order.del","0");
        $dispatch_cn = $this->db->get()->num_rows();
        
        echo json_encode(array('order_list' => $order_list,"pending_count" => $pending_cn,"assign_count" => $assign_cn,"invoice_count" => $invoice_cn,"packing_count" => $packing_cn,"dispatch_count" => $dispatch_cn));
    }
    
    public function order_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);       

        $id = $this->decrypt($data['order_id']);
        
        $this->db->select('whp_order.*,whp_warehouse.warehouse_name as assign_warehouse_name');
        $this->db->from("whp_order");
        $this->db->join("whp_warehouse","whp_warehouse.id = whp_order.assign_warehouse","left");
        $this->db->where("whp_order.id",$id);
        $this->db->where("whp_order.del","0");
        $order = $this->db->get()->row_array();
        
        // print_r($order);
        // exit;

        $this->db->select('whp_order_item.*');
        $this->db->from("whp_order_item");
        $this->db->where("whp_order_item.order_id",$order['id']);
        $this->db->where("whp_order_item.del","0");
        $order_item = $this->db->get()->result_array();
        
        if(isset($data['warehouse_id']) && $data['warehouse_id'])
        {
            $i=0;
            foreach($order_item as $item)
            {
                $ware_stock = $this->db->select("id,current_stock")->from("whp_warehouse_product")->where("warehouse_id",$data['warehouse_id'])->where("cat_no",$item['cat_no'])->where("del",'0')->get()->row_array();
                if($ware_stock)
                    $order_item[$i]['available_stock'] = $ware_stock['current_stock'];
                else
                     $order_item[$i]['available_stock'] = 0;
                $i++;
            }
        }
        
        $i=0;
        foreach($order_item as $val)
        {
           $this->db->select("whp_warehouse_product.warehouse_id,whp_warehouse.warehouse_name,whp_warehouse_product.current_stock");
           $this->db->from("whp_warehouse_product");
           $this->db->join("whp_warehouse","whp_warehouse.id = whp_warehouse_product.warehouse_id","left");
           $this->db->where("whp_warehouse_product.cat_no",$val['cat_no']);
           $this->db->where("whp_warehouse_product.del","0");
           $stock = $this->db->get()->result_array();
           $order_item[$i]['warehouse_stock'] = $stock;
           $i++;
        }
        
        $this->db->select('whp_order_payment_mode.*');
        $this->db->from("whp_order_payment_mode");
        $this->db->where("whp_order_payment_mode.order_id",$order['id']);
        $this->db->where("whp_order_payment_mode.del","0");
        $order_payment = $this->db->get()->result_array();
        
        $this->db->select('whp_order_credit_payment.*');
        $this->db->from("whp_order_credit_payment");
        $this->db->where("whp_order_credit_payment.order_id",$order['id']);
        $this->db->where("whp_order_credit_payment.del","0");
        $order_credit_detail = $this->db->get()->row_array();
        
        if($order_credit_detail && Count($order_credit_detail)>0)
        {
            $order_credit_detail = $order_credit_detail;
        }
        else
        {
            $order_credit_detail =[];
        }
        
        $this->db->select('whp_customer.*');
        $this->db->from("whp_customer");
        $this->db->where("whp_customer.id",$order['customer_id']);
        // if($order['user_type'] == 'customer')
        // {
        // }
        // else
        // {
        //     $this->db->where("whp_customer.user_id",$order['user_id']);
        // }
        $this->db->where("whp_customer.del","0");
        $customer = $this->db->get()->row_array();
        
        if($order['user_type'] == 'customer')
        {
            $user = [];
        }
        else
        {
            $this->db->select('whp_user.*');
            $this->db->from("whp_user");
            $this->db->where("whp_user.id",$order['user_id']);
            $this->db->where("whp_user.del","0");
            $user = $this->db->get()->row_array();
        }
            
        
        $packing = array();
        $this->db->select('whp_order_packing.id as packing_id,whp_order_packing.item,whp_order_packing.*,whp_customer.*');
        $this->db->from("whp_order_packing");
        $this->db->join("whp_customer","whp_customer.id = whp_order_packing.customer_id","left");
        $this->db->where("whp_order_packing.order_id",$order['id']);
        $this->db->where("whp_order_packing.del","0");
        $packing = $this->db->get()->result_array();
        
        $this->db->select('whp_order_dispatch.id,whp_order_dispatch.date_created,whp_order_dispatch.total_item,whp_order_dispatch.order_id,whp_warehouse.warehouse_name,whp_customer.name');
        $this->db->from("whp_order_dispatch");
        $this->db->where("whp_order_dispatch.order_id",$order['id']);
        $this->db->join("whp_warehouse","whp_warehouse.id = whp_order_dispatch.warehouse_id","left");
        $this->db->join("whp_customer","whp_customer.id = whp_order_dispatch.customer_id","left");
        $this->db->where("whp_order_dispatch.del","0");
        $dispatch = $this->db->get()->row_array();
        
        echo json_encode(array('order_detail' => $order, 'order_item' => $order_item, 'order_credit_detail' => $order_credit_detail, 'order_payment' => $order_payment, 'customer_detail' => $customer, 'user_detail' =>$user, 'packing_list' => $packing,"dispatch" => $dispatch));
    }
    
    public function order_payment()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("whp_order.date_created,whp_order.payment_varification,whp_order.status,whp_order.id as order_id,whp_customer.*,whp_user.name as created_by_name,SUM(whp_order_payment_mode.amount) as total_payment");
        $this->db->from("whp_order");
        $this->db->join("whp_customer","whp_order.customer_id = whp_customer.id","left");
        $this->db->join("whp_order_payment_mode","whp_order_payment_mode.order_id = whp_order.id","left");
        $this->db->join("whp_user","whp_order.user_id = whp_user.id","left");
        $this->db->group_by("whp_order.id");
        $this->db->order_by("whp_order.id","desc");
        $this->db->where("whp_order.del","0");
        $tmp_db = clone $this->db;
        if(isset($data['pagelimit']) && $data['pagelimit'])
        {
            $this->db->limit($data['pagelimit'],$data['start']);
        }
        $payment_list = $this->db->get()->result_array();
        
        foreach ($payment_list as $i => $v)
		{
		    $payment_mods = $this->db->select("*")->from("whp_order_payment_mode")->where("order_id",$v["order_id"])->where("del","0")->get()->result_array();
		    
		    $credit_payment = $this->db->select("*")->from("whp_order_credit_payment")->where("order_id",$v["order_id"])->where("del","0")->get()->row_array();
		    
		    $payment_list[$i]['credit_payment'] = $credit_payment;
		    $payment_list[$i]['payment_modes'] = $payment_mods;
		    $payment_list[$i]['ecrpt_id'] =  $this->encrypt($v["order_id"]);
		}
        
        $payment_count = $tmp_db->get()->num_rows();
        echo json_encode(array("payment_list" => $payment_list, 'payment_cn' => $payment_count));
    }
    
    public function varify_payment()
    {
        $data = json_decode(file_get_contents("php://input"),true);
             
             // print_r($data);
        if($data['action'] == 'varify')
        {
        	// echo "true";
        	$this->db->query("update whp_order_payment_mode set varify = 'verified' where id='".$data['id']."' limit 1");
        }
        else
        {
        	// echo "false";
        	$this->db->query("update whp_order_payment_mode set varify = 'pending' where id='".$data['id']."' limit 1");        	
        }

        
        $order_id = $this->db->select("order_id")->from("whp_order_payment_mode")->where("id",$data['id'])->where("del","0")->get()->row_array();
        
        $payments = $this->db->select("id")->from("whp_order_payment_mode")->where("order_id",$order_id['order_id'])->where("varify","pending")->where("del","0")->get()->num_rows();
        if($payments == 0)
        {
            $this->db->query("update whp_order set payment_varification = 'verified' where id='".$order_id['order_id']."' limit 1");
        }
        else
        {
        	$this->db->query("update whp_order set payment_varification = 'not verified' where id='".$order_id['order_id']."' limit 1");
        }
        
        echo json_encode("success");
    }
    
    public function delete_order()
    {
        $id = json_decode(file_get_contents("php://input"),true);
       
        $this->db->query("update whp_order set del='1' where id = '".$id."'");
        
        $this->db->query("update whp_order_credit_payment set del='1' where order_id = '".$id."'");
        
        $this->db->query("update whp_order_item set del='1' where order_id = '".$id."'");
        
        $this->db->query("update whp_order_payment_mode set del='1' where order_id = '".$id."'");
        
        echo json_encode("success");
    }
    
    public function update_order()
    {
        $data = json_decode(file_get_contents("php://input"),true);
       
        $this->db->query("update whp_order_item set qty='".$data['order']['qty']."', price='".$data['order']['price']."' where id='".$data['order']['id']."' limit 1");
        $this->db->query("update whp_order set order_total='".$data['order_total']."' where id='".$data['order']['order_id']."' limit 1");
        echo json_encode("success");
    }
    
    public function update_order_total()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        $this->db->query("update whp_order set order_total='".$data['order_total']."' where id='".$data['order_id']."' limit 1");
        echo json_encode("success");
    }
    
    public function assign_order()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->query("update whp_order set status='".$data['status']."', assign_warehouse='".$data['assign_warehouse']."' where id='".$data['id']."' limit 1");
        
        $warehouse = $this->db->query("select warehouse_name from whp_warehouse where id='".$data['assign_warehouse']."' and del='0' ")->row_array();
        
        echo json_encode($warehouse);
    }
    
    public function get_assign_order()
    {
        $data = json_decode(file_get_contents("php://input"),true);
       
        $order_list=array();
       
    	if($data['page_type'] == 'packing_pend')
		{	
			$this->db->select("whp_order.*,whp_order.id as order_id,whp_customer.*,whp_order.total_item as packing_count");
            $this->db->from("whp_order");
            $this->db->where("whp_order.del","0");
            $this->db->where("whp_order.packing_flag","0");
            $this->db->join("whp_customer","whp_order.customer_id = whp_customer.id","left");
            if(isset($data['search']['search_val']) && $data['search']['search_val']!='')
            {
                $this->db->group_start();
                $this->db->like("whp_customer.name",$data['search']['search_val']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['search_val']);
                $this->db->group_end();
            }
            $this->db->order_by("whp_order.id","desc");
            $this->db->where("whp_order.assign_warehouse",$data['warehouse_id']);
            $order_list = $this->db->get()->result_array();

            foreach ($order_list as $i => $v)
    		{
    		    $order_list[$i]['ecrpt_id'] =  $this->encrypt($v["order_id"]);
    		}
		}

    	if($data['page_type'] == 'dispatch_pend')
		{
			$this->db->select("whp_order_packing.*,whp_order_packing.id as packing_id,whp_customer.*,whp_order_packing.item as packed_item");
            $this->db->from("whp_order_packing");
            $this->db->join("whp_customer","whp_order_packing.customer_id = whp_customer.id","left");
            $this->db->where("whp_order_packing.del","0");
            $this->db->where("whp_order_packing.invoice_no!=","");
            
            if(isset($data['search']['search_val']) && $data['search']['search_val']!='')
            {
                $this->db->group_start();
                $this->db->like("whp_customer.name",$data['search']['search_val']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['search_val']);
                $this->db->group_end();
            }
            $this->db->order_by("whp_order_packing.id","desc");
            $this->db->where("whp_order_packing.status",'pending');
            $this->db->where("whp_order_packing.warehouse_id",$data['warehouse_id']);
			$order_list = $this->db->get()->result_array();
			

			foreach ($order_list as $i => $v)
    		{
    		    $order_list[$i]['ecrpt_id'] =  $this->encrypt($v["packing_id"]);
    		}	    		
    	}
        if($data['page_type'] == 'dispatch') 
        {
        	// ,COUNT(whp_order_packing.order_id) as packing_count,SUM(whp_order_packing.item) as packed_item
            $this->db->select("whp_order_packing.*,whp_order_packing.id as packing_id,whp_customer.*,whp_order_packing.item as packed_item");
            $this->db->from("whp_order_packing");
            $this->db->join("whp_customer","whp_order_packing.customer_id = whp_customer.id","left");
            $this->db->where("whp_order_packing.del","0");
            if(isset($data['search']['search_val']) && $data['search']['search_val']!='')
            {
                $this->db->group_start();
                $this->db->like("whp_customer.name",$data['search']['search_val']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['search_val']);
                $this->db->group_end();
            }
            $this->db->order_by("whp_order_packing.id","desc");
            $this->db->where("whp_order_packing.status",'deliver');
            $this->db->where("whp_order_packing.warehouse_id",$data['warehouse_id']);
            $order_list = $this->db->get()->result_array();
            
            foreach ($order_list as $i => $v)
    		{
    		    $order_list[$i]['ecrpt_id'] =  $this->encrypt($v["packing_id"]);
    		}
        }
        
        $this->db->select("whp_order.id");
        $this->db->from("whp_order");
        $this->db->where("whp_order.del","0");
        $this->db->where("whp_order.packing_flag","0");
        $this->db->where("whp_order.assign_warehouse",$data['warehouse_id']);
        $pack_pend_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_order_packing.id");
        $this->db->from("whp_order_packing");
        $this->db->where("whp_order_packing.del","0");
        $this->db->where("whp_order_packing.status","pending");
        $this->db->where("whp_order_packing.invoice_no!=","");
        $this->db->where("whp_order_packing.warehouse_id",$data['warehouse_id']);
        $disp_pend_cn = $this->db->get()->num_rows();
        
        $this->db->select("whp_order_packing.id");
        $this->db->from("whp_order_packing");
        $this->db->where("whp_order_packing.del","0");
        $this->db->where("whp_order_packing.status","deliver");
        $this->db->where("whp_order_packing.warehouse_id",$data['warehouse_id']);
        $dispatch_cn = $this->db->get()->num_rows();
        
        // print_r($order_list);
        

		echo json_encode(array("order_list" => $order_list, 'pack_pend_cn' => $pack_pend_cn,'disp_pend_cn' => $disp_pend_cn, 'dispatch_cn' => $dispatch_cn));
    }
    
    public function order_dispatch_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $id = $this->decrypt($data['order_id']);
        $order_packing_item = array();
        
        $this->db->select('whp_order_packing.id as packing_id,whp_order_packing.item,whp_order_packing.*,whp_customer.*');
        $this->db->from("whp_order_packing");
        $this->db->join("whp_customer","whp_customer.id = whp_order_packing.customer_id","left");
        $this->db->where("whp_order_packing.order_id",$id);
        $this->db->where("whp_order_packing.del","0");
        $packing = $this->db->get()->result_array();
        
        $j=0;
        if($packing)
        {
            foreach($packing as $data)
            {
                $this->db->select('whp_order_packing_item.*');
                $this->db->from("whp_order_packing_item");
                $this->db->where("whp_order_packing_item.order_packing_id",$data['packing_id']);
                $this->db->where("whp_order_packing_item.del","0");
                $order_packing_item = $this->db->get()->result_array();
                $packing[$j]['items'] = $order_packing_item;
                $j++;
            }
        }
        
        
        $this->db->select('whp_order.*,whp_warehouse.warehouse_name as assign_warehouse_name');
        $this->db->from("whp_order");
        $this->db->join("whp_warehouse","whp_warehouse.id = whp_order.assign_warehouse","left");
        $this->db->where("whp_order.id",$id);
        $this->db->where("whp_order.del","0");
        $order = $this->db->get()->row_array();
        
        $this->db->select('whp_order_item.*');
        $this->db->from("whp_order_item");
        $this->db->where("whp_order_item.order_id",$order['id']);
        $this->db->where("whp_order_item.qty!='0'");
        $this->db->where("whp_order_item.del","0");
        $order_item = $this->db->get()->result_array();
       
        $i=0;
        foreach($order_item as $item)
        {
            $ware_stock = $this->db->select("id,current_stock")->from("whp_warehouse_product")->where("warehouse_id",$data['warehouse_id'])->where("cat_no",$item['cat_no'])->where("del",'0')->get()->row_array();
            if($ware_stock)
                $order_item[$i]['available_stock'] = $ware_stock['current_stock'];
            else
                 $order_item[$i]['available_stock'] = 0;
            $i++;
        }
    
       
        $this->db->select('whp_order_payment_mode.*');
        $this->db->from("whp_order_payment_mode");
        $this->db->where("whp_order_payment_mode.order_id",$order['id']);
        $this->db->where("whp_order_payment_mode.del","0");
        $order_payment = $this->db->get()->result_array();
        
        $this->db->select('whp_order_credit_payment.*');
        $this->db->from("whp_order_credit_payment");
        $this->db->where("whp_order_credit_payment.order_id",$order['id']);
        $this->db->where("whp_order_credit_payment.del","0");
        $order_credit_detail = $this->db->get()->row_array();
        
        $this->db->select('whp_customer.*');
        $this->db->from("whp_customer");
        if($order['user_type'] == 'customer')
        {
            $this->db->where("whp_customer.id",$order['customer_id']);
        }
        else
        {
            $this->db->where("whp_customer.user_id",$order['user_id']);
        }
        $this->db->where("whp_customer.del","0");
        $customer = $this->db->get()->row_array();
                        
        if($order['user_type'] == 'customer')
        {
            $user = [];
        }
        else
        {
            $this->db->select('whp_user.*');
            $this->db->from("whp_user");
            $this->db->where("whp_user.id",$order['user_id']);
            $this->db->where("whp_user.del","0");
            $user = $this->db->get()->row_array();
        }

        $this->db->select('whp_order_dispatch.id,whp_order_dispatch.date_created,whp_order_dispatch.total_item,whp_order_dispatch.order_id,whp_warehouse.warehouse_name,whp_customer.name');
        $this->db->from("whp_order_dispatch");
        $this->db->where("whp_order_dispatch.order_id",$id);
        $this->db->join("whp_warehouse","whp_warehouse.id = whp_order_dispatch.warehouse_id","left");
        $this->db->join("whp_customer","whp_customer.id = whp_order_dispatch.customer_id","left");
        $this->db->where("whp_order_dispatch.del","0");
        $dispatch = $this->db->get()->row_array();
        
        echo json_encode(array('order_detail' => $order, 'order_item' => $order_item, 'order_credit_detail' => $order_credit_detail, 'order_payment' => $order_payment, 'customer_detail' => $customer, 'user_detail' =>$user, 'packing' => $packing, "packing_item" => $order_packing_item, 'dispatch' => $dispatch));
    }

     public function order_dispatch_pend()
     {
     	$data = json_decode(file_get_contents("php://input"),true);
     	$id = $this->decrypt($data['packing_id']);
               
        $order_packing_item = array();
        
        $this->db->select('whp_order_packing.id as packing_id,whp_order_packing.item,whp_order_packing.*,whp_customer.*');
        $this->db->from("whp_order_packing");
        $this->db->join("whp_customer","whp_customer.id = whp_order_packing.customer_id","left");
        $this->db->where("whp_order_packing.id",$id);
        $this->db->where("whp_order_packing.del","0");
        $packing = $this->db->get()->row_array();

        $this->db->select('whp_order_packing_item.*');
        $this->db->from("whp_order_packing_item");
        $this->db->where("whp_order_packing_item.order_packing_id",$packing['packing_id']);
        $this->db->where("whp_order_packing_item.del","0");
        $order_packing_item = $this->db->get()->result_array();

        $this->db->select('whp_order.*,whp_warehouse.warehouse_name as assign_warehouse_name');
        $this->db->from("whp_order");
        $this->db->join("whp_warehouse","whp_warehouse.id = whp_order.assign_warehouse","left");
        $this->db->where("whp_order.id",$packing['order_id']);
        $this->db->where("whp_order.del","0");
        $order = $this->db->get()->row_array();
                
        $this->db->select('whp_order_item.*');
        $this->db->from("whp_order_item");
        $this->db->where("whp_order_item.order_id",$order['id']);
        $this->db->where("whp_order_item.qty!='0'");
        $this->db->where("whp_order_item.del","0");
        $order_item = $this->db->get()->result_array();

        $this->db->select('whp_customer.*');
        $this->db->from("whp_customer");
        if($order['user_type'] == 'customer')
        {
            $this->db->where("whp_customer.id",$order['customer_id']);
        }
        else
        {
            $this->db->where("whp_customer.user_id",$order['user_id']);
        }
        $this->db->where("whp_customer.del","0");
        $customer = $this->db->get()->row_array();

		echo json_encode(array('order_detail' => $order, 'order_item' => $order_item, 'customer_detail' => $customer, 'packing' => $packing, "packing_item" => $order_packing_item));

     }
    
    
    public function order_packing()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        $order_id = $data['packing_item'][0]['order_id'];
        $date_created = date("Y-m-d H:i:s a");

        $packing = array(
            'date_created' => $date_created,
            'warehouse_id' => $data['warehouse_id'],
            'order_id' => $order_id,
            'customer_id' => $data['customer_id'],
            'item' => count($data['packing_item']),
            'status' => 'pending'
        );
        
        $this->db->insert("whp_order_packing",$packing);
        $last_id = $this->db->insert_id();
        
        for($i=0;$i<count($data['packing_item']);$i++)
        {
            $item = array(
                'date_created' => $date_created,
                'order_packing_id' => $last_id,
                'brand' => $data['packing_item'][$i]['brand'],
                'category' => $data['packing_item'][$i]['category'],
                'sub_category' => $data['packing_item'][$i]['sub_category'],
                'product_name' => $data['packing_item'][$i]['product_name'],
                'cat_no' => $data['packing_item'][$i]['cat_no'],
                'price' => $data['packing_item'][$i]['price'],
                'qty' => $data['packing_item'][$i]['qty'],
                'pack_qty' => $data['packing_item'][$i]['pack_qty'],
            );
            
            $this->db->insert("whp_order_packing_item",$item);
            
            $qty = $this->db->query("select qty,id from whp_order_item where order_id = '".$data['packing_item'][$i]['order_id']."' AND cat_no = '".$data['packing_item'][$i]['cat_no']."' AND del = '0'")->row_array();
            
            $update_data = array(
                'qty' => (int)($qty['qty']) - (int)($data['packing_item'][$i]['pack_qty'])
            );
            $this->db->where("id",$qty['id']);
            $this->db->update("whp_order_item",$update_data);
        }

		$num_row = $this->db->select("id")->from("whp_order_item")->where("order_id",$order_id)->where("qty !='0'")->where("del","0")->get()->num_rows();
		if($num_row == 0)
		{
			$this->db->query("update whp_order set packing_flag = 1 where id='".$order_id."' limit 1");
		}
        
        echo json_encode("success");
    }
    
    public function get_packing_item()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("*");
        $this->db->from("whp_order_packing_item");
        $this->db->where("order_packing_id",$data);
        $this->db->where("del","0");
        $item_list = $this->db->get()->result_array();
        
        echo json_encode(array("item_list" => $item_list));
    }
    
    public function generate_invoice()
    {
        $invoice = array(
            'invoice_date' => json_decode($_POST['invoice_date']),
            'invoice_no' => json_decode($_POST['invoice_no']),
            'net_amount' => json_decode($_POST['invoice_amount'])
        );
        
        $this->db->where("id",json_decode($_POST['packing_id']));
        
        $data = $this->db->update("whp_order_packing",$invoice);
       
        if(isset($_FILES['image']))
        {
            $type=$_FILES['image']["type"];
            $name=$_FILES['image']["name"];
          	$uploaded_path = $_SERVER['DOCUMENT_ROOT'].'/wheel_power_api/'. DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR ;
    	    $tmp_name=$_FILES['image']["tmp_name"];
    		$img_name=uniqid();
    		$tmp_ext=explode(".",$name);
    		$ext=strtolower(end($tmp_ext));
    		$uploaded_path.=$img_name;
    		$uploaded_path.=".".$ext;
    		
			if(move_uploaded_file($tmp_name, $uploaded_path))
			{
		        $data = array(
                    'image' => $img_name.'.'.$ext
                );
                $this->db->where("id",json_decode($_POST['packing_id']));
                $this->db->update('whp_order_packing', $data);
    		}
        }
        $order = $this->db->select("order_id")->from("whp_order_packing")->where("id",json_decode($_POST['packing_id']))->where('del','0')->get()->row_array();
        
        $invoice = $this->db->select("order_id")->from("whp_order_packing")->where("order_id",$order['order_id'])->where("invoice_no =''")->where('del','0')->get()->num_rows();
        
        if($invoice == 0)
        {
            $this->db->query("update whp_order set invoice_flag = '1' where id = '".$order['order_id']."'");
        }
        
        ////////////// For All item should be packed /////////////////////
        
        // $order = $this->db->select("order_id")->from("whp_order_packing")->where("id",json_decode($_POST['packing_id']))->where('del','0')->get()->row_array();
        
        // $order_data = $this->db->select("total_item")->from("whp_order")->where("id",$order['order_id'])->where('del','0')->get()->row_array();
        
        // $invoice = $this->db->select("order_id,SUM(item) as packed_item")->from("whp_order_packing")->where("order_id",$order['order_id'])->where("invoice_no !=''")->where('del','0')->get()->row_array();
        
        // if($invoice['packed_item'] == $order_data['total_item'])
        // {
        //     $this->db->query("update whp_order set packing_flag = '1' where id = '".$order['order_id']."'");
        // }
        echo json_encode("success");
	}
	
	
	public function order_packing_detail()
	{
	    $data = json_decode(file_get_contents("php://input"),true);
	    $id = $this->decrypt($data);
	    
	    $this->db->select('whp_order_packing.id as packing_id,whp_order_packing.item,whp_order_packing.*,whp_customer.*,whp_order.id as order_id,whp_order.date_created as order_date');
        $this->db->from("whp_order_packing");
        $this->db->join("whp_customer","whp_customer.id = whp_order_packing.customer_id","left");
        $this->db->join("whp_order","whp_order.id = whp_order_packing.order_id","left");
        $this->db->where("whp_order_packing.id",$id);
        $this->db->where("whp_order_packing.del","0");
        $packing = $this->db->get()->row_array();
    
        $this->db->select('whp_order_packing_item.*');
        $this->db->from("whp_order_packing_item");
        $this->db->where("whp_order_packing_item.order_packing_id",$id);
        $this->db->where("whp_order_packing_item.del","0");
        $order_packing_item = $this->db->get()->result_array();
        echo json_encode(array("packing"=>$packing ,'packing_item' =>$order_packing_item));
    }
	
	public function order_dispatch()
	{
	    $data = json_decode(file_get_contents("php://input"),true);
	    $id = $this->decrypt($data);
	        
	    $date_created = date("Y-m-d H:i:s a");
	    
	    $this->db->select('whp_order_packing.*');
        $this->db->from("whp_order_packing");
        $this->db->where("whp_order_packing.id",$id);
        $this->db->where("whp_order_packing.del","0");
        $packing = $this->db->get()->row_array();
                
        $this->db->select('whp_order_packing_item.*');
        $this->db->from("whp_order_packing_item");
        $this->db->where("whp_order_packing_item.order_packing_id",$packing['id']);
        $this->db->where("whp_order_packing_item.del","0");
        $order_packing_item = $this->db->get()->result_array();
        
     //    print_r($order_packing_item);
	    // exit;
        foreach($order_packing_item as $item)
        {   
            $result = $this->db->query("select id,current_stock from whp_warehouse_product where warehouse_id = '".$packing['warehouse_id']."' AND cat_no='".$item['cat_no']."' AND del='0'")->row_array();
            $update = array(
                'current_stock' => (int)$result['current_stock'] - (int)$item['pack_qty']
            );
            $this->db->where("id",$result['id']);
            $this->db->update("whp_warehouse_product",$update);
        }
        $this->db->query("update whp_order_packing set status = 'deliver' where id='".$id."'");
        
        $done = $this->db->query("update whp_order set status = 'deliver' where id='".$packing['order_id']."'");
                
        if($done)
        {
            $msg = "success";
            $this->db->select('whp_order.*');
            $this->db->from("whp_order");
            $this->db->where("whp_order.id",$packing['order_id']);
            $this->db->where("whp_order.del","0");
            $order = $this->db->get()->row_array();
            
            $dispatch = array(
                'date_created' => $date_created,
                'warehouse_id' => $order['assign_warehouse'],
                'order_id' => $id,
                'customer_id' => $order['customer_id'],
                'total_item' => $order['total_item']
            );
            
            $this->db->insert("whp_order_dispatch",$dispatch);
        }
        else
        {
            $msg = "error";
        }



	    // $this->db->select('whp_order_packing.*');
     //    $this->db->from("whp_order_packing");
     //    $this->db->where("whp_order_packing.order_id",$id);
     //    $this->db->where("whp_order_packing.del","0");
     //    $packing = $this->db->get()->result_array();
        
     //    foreach($packing as $val)
     //    {
     //        $this->db->select('whp_order_packing_item.*');
     //        $this->db->from("whp_order_packing_item");
     //        $this->db->where("whp_order_packing_item.order_packing_id",$val['id']);
     //        $this->db->where("whp_order_packing_item.del","0");
     //        $order_packing_item = $this->db->get()->result_array();
            
     //        foreach($order_packing_item as $item)
     //        {   
     //            $result = $this->db->query("select id,current_stock from whp_warehouse_product where warehouse_id = '".$val['warehouse_id']."' AND cat_no='".$item['cat_no']."' AND del='0'")->row_array();
     //            $update = array(
     //                'current_stock' => (int)$result['current_stock'] - (int)$item['pack_qty']
     //            );
     //            $this->db->where("id",$result['id']);
     //            $this->db->update("whp_warehouse_product",$update);
     //        }
     //        $this->db->query("update whp_order_packing set status = 'deliver' where id='".$order_packing_item[0]['order_packing_id']."'");
     //    }
     //    $done = $this->db->query("update whp_order set status = 'deliver' where id='".$id."'");
                
     //    if($done)
     //    {
     //        $msg = "success";
     //        $this->db->select('whp_order.*');
     //        $this->db->from("whp_order");
     //        $this->db->where("whp_order.id",$id);
     //        $this->db->where("whp_order.del","0");
     //        $order = $this->db->get()->row_array();
            
     //        $dispatch = array(
     //            'date_created' => $date_created,
     //            'warehouse_id' => $order['assign_warehouse'],
     //            'order_id' => $id,
     //            'customer_id' => $order['customer_id'],
     //            'total_item' => $order['total_item']
     //        );
            
     //        $this->db->insert("whp_order_dispatch",$dispatch);
     //    }
     //    else
     //    {
     //        $msg = "error";
     //    }
        echo json_encode($msg);
	}
	
	public function get_payments()
	{
	    $order_id = json_decode(file_get_contents("php://input"),true);
	    
	    $this->db->select('whp_order_payment_mode.*');
        $this->db->from("whp_order_payment_mode");
        $this->db->where("whp_order_payment_mode.order_id",$order_id);
        $this->db->where("whp_order_payment_mode.del","0");
        $order_payment = $this->db->get()->result_array();
        
        echo json_encode(array("payment_list" => $order_payment));
	}
}