<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class customer extends MY_Controller {


    public function __construct() {
        parent:: __construct();
        $this->load->model('manufacturerModel');
        $this->load->database();
    }
    
    public function get_all_customer()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        
        // print_r($data);
        
        $this->db->select("whp_customer.*,whp_user.name as created_by_name");
        $this->db->from("whp_customer");
        if(isset($data['search']) && $data['search'])
        {
            if(isset($data['search']['search_customer']) && $data['search']['search_customer'])
            {
                $this->db->group_start();
                $this->db->or_like("whp_customer.name",$data['search']['search_customer']);
                $this->db->group_end();
            }
        }
        $this->db->join("whp_user","whp_user.id = whp_customer.user_id","left");
        $this->db->where("whp_customer.del","0");
        $tmp_db = clone $this->db;
        if(isset($data["pagelimit"]) && $data["pagelimit"]!=0)
        {
            $this->db->limit(@$data["pagelimit"],@$data["start"]);
        }
        
        $customer = $this->db->get()->result_array();
        $total_rec = $tmp_db->get()->num_rows();
        
        foreach ($customer as $i => $v)
        {
            $customer[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
        }
        
        $result = array("customer_list" => $customer,"total_rec" => $total_rec);
        echo json_encode($result);
    }
    
    public function get_customer_detail()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        $customer_id = $this->decrypt($data);
        
        $this->db->select("*");
        $this->db->from("whp_customer");
        $this->db->where("id",$customer_id);
        $this->db->where("del","0");
        $customer = $this->db->get()->row_array();
        
        $this->db->select("whp_order.*");
        $this->db->from("whp_order");
        $this->db->where("whp_order.customer_id",$customer_id);
        $this->db->where("whp_order.del","0");
        $order_list = $this->db->get()->result_array();
        
        foreach ($order_list as $i => $v)
        {
            $order_list[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
        }
        $result = array("customer" => $customer,"order_list" => $order_list);
        echo json_encode($result);
    }
    
}