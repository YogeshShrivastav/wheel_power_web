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
        
        $this->db->select("whp_order.date_created,whp_order.status,whp_order.id as order_id,whp_customer.*,whp_user.name as created_by_name");
        $this->db->from("whp_order");
        $this->db->join("whp_customer","whp_order.customer_id = whp_customer.id","left");
        $this->db->join("whp_user","whp_order.user_id = whp_user.id","left");
        $this->db->where("whp_order.del","0");
        $tmp_db = clone $this->db;
        $order_list = $this->db->get()->result_array();
        
        
        $order_count = $tmp_db->get()->num_rows();
        
        foreach ($order_list as $i => $v)
		{
		    $total_item = $this->db->select("id")->from("whp_order_item")->where("del","0")->where("order_id",$v["order_id"])->get()->num_rows();
		    
		    $order_list[$i]['total_item'] =  $total_item;
			$order_list[$i]['ecrpt_id'] =  $this->encrypt($v["order_id"]);
		}
        
        echo json_encode(array('order_list' => $order_list,"order_count" => $order_count));
    }
    
    public function order_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        $id = $this->decrypt($data);
        
        $this->db->select('whp_order.*');
        $this->db->from("whp_order");
        $this->db->where("whp_order.id",$id);
        $this->db->where("whp_order.del","0");
        $order = $this->db->get()->row_array();
        
        $this->db->select('whp_order_item.*');
        $this->db->from("whp_order_item");
        $this->db->where("whp_order_item.order_id",$order['id']);
        $this->db->where("whp_order_item.del","0");
        $order_item = $this->db->get()->result_array();
        
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
        $this->db->where("whp_customer.id",$order['customer_id']);
        $this->db->where("whp_customer.user_id",$order['user_id']);
        $this->db->where("whp_customer.del","0");
        $customer = $this->db->get()->row_array();
        
        $this->db->select('whp_user.*');
        $this->db->from("whp_user");
        $this->db->where("whp_user.id",$order['user_id']);
        $this->db->where("whp_user.del","0");
        $user = $this->db->get()->row_array();
        
        echo json_encode(array('order_detail' => $order, 'order_item' => $order_item, 'order_credit_detail' => $order_credit_detail, 'order_payment' => $order_payment, 'customer_detail' => $customer, 'user_detail' =>$user));
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
        
        $payment_count = $tmp_db->get()->num_rows();
        // print_r($order_list);
        foreach ($payment_list as $i => $v)
		{
			$payment_list[$i]['ecrpt_id'] =  $this->encrypt($v["order_id"]);
		}
        echo json_encode(array("payment_list" => $payment_list, 'payment_cn' => $payment_count));
    }
    
    public function varify_payment()
    {
        $id = json_decode(file_get_contents("php://input"),true);
        
        $this->db->query("update whp_order set payment_varification = 'varify' where id='".$id."' limit 1");
        
        echo json_encode("success");
    }
    
}