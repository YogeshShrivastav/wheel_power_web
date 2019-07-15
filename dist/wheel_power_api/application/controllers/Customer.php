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
        
        $this->db->select("whp_customer.*,whp_user.name as created_by_name");
        $this->db->from("whp_customer");
        if(isset($data['search']) && $data['search'])
        {
            if(isset($data['search']['search_customer']) && $data['search']['search_customer'])
            {
                $this->db->group_start();
                $this->db->or_like("whp_customer.name",$data['search']['search_customer']);
                $this->db->or_like("whp_customer.mobile_1",$data['search']['search_customer']);
                $this->db->group_end();
            }
        }
        $this->db->join("whp_user","whp_user.id = whp_customer.user_id","left");
        $this->db->where("whp_customer.del","0");
        $this->db->order_by("whp_customer.id","desc");
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
        
        $this->db->select("whp_customer.*");
        $this->db->from("whp_customer");
        $this->db->where("whp_customer.id",$customer_id);
        $this->db->where("whp_customer.del","0");
        $customer = $this->db->get()->row_array();
        
        $this->db->select("whp_order.*");
        $this->db->from("whp_order");
        $this->db->where("whp_order.customer_id",$customer_id);
        $this->db->where("whp_order.del","0");
        $order_list = $this->db->get()->result_array();
        
        foreach ($order_list as $i => $v)
		{
			if($v['user_type'] == "customer")
            {
                $order_list[$i]['created_by_name'] = $customer['name'];
            }
            else
            {
                $user = $this->db->select("name")->from("whp_user")->where("del","0")->where("id",$v["user_id"])->get()->row_array();
                $order_list[$i]['created_by_name'] = $user['name'];
            }
			$order_list[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        $result = array("customer" => $customer,"order_list" => $order_list);
        echo json_encode($result);
    }

    public function update_customer()
    {
    	$_POST = json_decode(file_get_contents("php://input"),true);

    	if(isset($_POST['mobile_2']) && $_POST['mobile_2'])
        {  $mobile_2 = $_POST['mobile_2'];  }
        else
        { $mobile_2 = ""; }
        
        if(isset($_POST['email']) && $_POST['email'])
        {  $email = $_POST['email'];  }
        else
        { $email = ""; }

        if(isset($_POST['pincode']) && $_POST['pincode'])
        {  $pincode = $_POST['pincode'];  }
        else
        { $pincode = ""; }

    	$user_data = array(
          'name' => $_POST['name'],
          'mobile_1' => $_POST['mobile_1'],
          'mobile_2' => $mobile_2,
          'email' => $email,
          'address' => $_POST['address'],
          'state' => $_POST['state'],
          'district' => $_POST['district'],
          'city' => $_POST['city'],
          'pincode' => $pincode,
          );

		$this->db->where("whp_customer.id",$_POST['id']);
		$this->db->update("whp_customer",$user_data);
      
		echo json_encode("success");
    }
    
}