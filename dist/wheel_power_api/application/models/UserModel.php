<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserModel extends MY_Model
{

	
    public function submit_user()
    {
        if($_POST["admin_type"]=='SALES USER')
        { $role="3"; }
        if($_POST["admin_type"]=='Admin')
        { $role="1"; }
        if($_POST["admin_type"]=='Sub-Admin')
        { $role="2"; }
        
        if(isset($_POST["email"]) && $_POST["email"])
        { $email = $_POST["email"]; }
        else
        { $email =""; }
        
        if(isset($_POST["address"]) && $_POST["address"])
        { $address = $_POST["address"]; }
        else
        { $address =""; }
        
        if(isset($_POST["country"]) && $_POST["country"])
        { $country = $_POST["country"]; }
        else
        { $country =""; }
        
        if(isset($_POST["state"]) && $_POST["state"])
        { $state = $_POST["state"]; }
        else
        { $state =""; }
        
        if(isset($_POST["district"]) && $_POST["district"])
        { $district = $_POST["district"]; }
        else
        { $district =""; }
        
        if(isset($_POST["city"]) && $_POST["city"])
        { $city = $_POST["city"]; }
        else
        { $city =""; }
        
        if(isset($_POST["pincode"]) && $_POST["pincode"])
        { $pincode = $_POST["pincode"]; }
        else
        { $pincode =""; }
        
        $_POST["status"]='Active';
        $sql=$this->db->query("insert into whp_user (date_created,created_by,role,name,email,mobile,address,country,state,district,city,pincode,status) values('".date('Y-m-d H:i:s')."','".$_POST["user_id"]."','".$role."','".$_POST["name"]."','".$email."','".$_POST["mobile"]."','".$address."','".$country."','".$state."','".$district."','".$city."','".$pincode."','".$_POST["status"]."')");
        
        $inserted_id = $this->db->insert_id();
        
        $username ="wpuser".$inserted_id;
        
        $array = array(
            'username' => $username,
            'password' => $_POST["mobile"]
            );
        
        $this->db->where("id",$inserted_id);
        $this->db->update("whp_user",$array);
        
        return $sql;
    }
    
    public function getall_user()
    {        
        $this->db->select("whp_user.*,whp_role.role");
        $this->db->from("whp_user");
        $this->db->join("whp_role","whp_user.role=whp_role.id","left");
        if(isset($_POST["search"]))
        {
            if($_POST["search"]["type"]==3)
            {   
                $this->db->where("whp_user.role",$_POST["search"]["type"]);
            }
            else if($_POST["search"]["type"] == 1 || $_POST["search"]["type"] == 2)
            {
                 $this->db->where("whp_user.role!=",3);
            }
            
            if(isset($_POST["search"]["search_user"]))
            {
                $this->db->group_start();
                $this->db->like("whp_user.name",$_POST["search"]["search_user"]);
                $this->db->or_like("whp_user.username",$_POST["search"]["search_user"]);
                $this->db->or_like("whp_user.mobile",$_POST["search"]["search_user"]);
                $this->db->group_end();
            }
            
        }
        
        $this->db->where("whp_user.del","0");
        $tmp_db=clone $this->db;
        $row=$tmp_db->get()->num_rows();
        $this->db->limit(@$_POST["pagelimit"],@$_POST["start"]);
        $this->db->order_by("whp_user.id","desc");
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
			$result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
     
        $array=array("data"=>$result,"row"=>$row);
        
        return $array;
    }
    
    public function update_user_status()
    {
        $sql=$this->db->query("update whp_user set status='".$_POST["status"]."' where id='".$_POST["id"]."'");
        
        return $sql;
    }
    
    public function get_userdetail()
    {
        $id=$this->decrypt($_POST['id']);
        $this->db->select("whp_user.*,whp_role.role");
        $this->db->from("whp_user");
        $this->db->join("whp_role","whp_user.role=whp_role.id","left");
        $this->db->where("whp_user.id",$id);
        $this->db->where("whp_user.del","0");
        $result=$this->db->get()->row_array();
        
        $this->db->select("whp_user_log.*,whp_user.name as created_by_name");
        $this->db->from("whp_user_log");
        $this->db->join("whp_user","whp_user.id = whp_user_log.created_by","left");
        $this->db->where("whp_user_log.user_id",$id);
        $this->db->where("whp_user_log.del","0");
        $log = $this->db->get()->result_array();
        
        $this->db->select("whp_order.*,whp_customer.name as customer_name,whp_customer.mobile_1,whp_customer.mobile_2");
        $this->db->from("whp_order");
        $this->db->join("whp_customer","whp_customer.id = whp_order.customer_id","left");
        $this->db->where("whp_order.user_id",$id);
        $this->db->where("whp_order.del","0");
        $order = $this->db->get()->result_array();
        
        foreach ($order as $i => $v)
		{
			$order[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
        
        $array = array("user_data" => $result, 'log' => $log, 'order_list' => $order);
        
        return $array;
    }
    
    public function update_user1(){
        // print_r($_POST);
        // exit;
        $date_created = date("Y-m-d h:i:s a");
        $data=array(
            'username'=> $_POST['username'],
            'password'=> $_POST['password'],
            'email'=> $_POST['email'],
            'mobile'=> $_POST['mobile'],
            'country'=> $_POST['country'],
            'state'=> $_POST['state'],
            'district'=> $_POST['district'],
            'city'=> $_POST['city'],
            'address'=> $_POST['address'],
            'pincode'=> $_POST['pincode']
        );
        $this->db->where('id',$_POST['id']);
        
        $this->db->query("insert into whp_user_log (date_created,created_by,user_id,log) values ('".$date_created."','".$_POST['created_by']."','".$_POST['id']."','Profile updated' )");
        
        $data=$this->db->update('whp_user',$data);
        return $data;
    }
      
}