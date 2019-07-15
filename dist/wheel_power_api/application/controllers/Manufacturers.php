<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class manufacturers extends MY_Controller {


    public function __construct() {
        parent:: __construct();
        $this->load->model('manufacturerModel');
        $this->load->database();
    }

    //To User Login


    public function get_countries(){
        $this->db->select('id,name');
        $this->db->from("countries");
        //$this->db->where("del","0");
        $query=$this->db->get()->result();
        //  = $this->db->get("countries");
        // echo "<pre>";
        echo json_encode($query,JSON_NUMERIC_CHECK);
    }

    public function get_state(){
        $this->db->DISTINCT();
        $this->db->select('state_name');
        $this->db->from("whp_postal_master");
        $this->db->where("del","0");
        $query = $this->db->get()->result_array();
        // echo "<pre>";
        echo json_encode($query);
    }
    public function get_district(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        $this->db->DISTINCT();
        $query = $this->db->select('district_name');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state']);
        $query = $this->db->order_by("district_name");
        $query = $this->db->get()->result_array();
        // echo "<pre>";
        echo json_encode($query);
    }

    public function get_city(){
       
        $_POST=json_decode(file_get_contents("php://input"),true);
        $this->db->DISTINCT();
        $query = $this->db->select('city');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state']);
        $query = $this->db->where('whp_postal_master.district_name',$_POST['district']);
        $query = $this->db->order_by("city");
        $query = $this->db->get()->result_array();
        // echo "<pre>";
        echo json_encode($query);
    }
    public function get_pincode(){
       
        $_POST=json_decode(file_get_contents("php://input"),true);
        $query = $this->db->select('pincode');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state']);
        $query = $this->db->where('whp_postal_master.district_name',$_POST['district']);
        $query = $this->db->get()->result_array();
        // echo "<pre>";
        echo json_encode($query);
    }



    public function get()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
       
        $manufacturer=new ManufacturerModel;
        $data=$manufacturer->get_manufacturer();
        
        echo json_encode($data);
        
        return $manufacturer;
    }
    public function get_manufacturer_list(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $manufacturer=new ManufacturerModel;
        $data=$manufacturer->get_manufacturer_list();
       
        if(sizeof($data)>0)
        { 
            $msg='Success';
        }
        else
        {
            $msg='Failed';
        }
        
        echo json_encode(array('data'=> $data,'msg'=>$msg));
        
        return $manufacturer;
    }

    public function store()
    {   
        
        $_POST=json_decode(file_get_contents("php://input"),true);
        // echo "<pre>";
        // print_r($_POST['mobile']);
        // exit;
        // $manufacturer=new ManufacturerModel;
        // $manufacturer->insert_manufacturer();
        if(isset($_POST['gst'])){$gst=$_POST['gst'];}else{$gst='';}
        if(isset($_POST['landline'])){$lnl=$_POST['landline'];}else{$lnl='';}
        if(isset($_POST['address'])){$adr=$_POST['address'];}else{$adr='';}
        $data = array(
            'name' => str_replace('"', '',$_POST['name']),
            'user_id' => str_replace('"', '',$_POST['user_id']),
            'contact_person' => str_replace('"', '',$_POST['contact_person']),
            'mobile' => (int)$_POST['mobile'],
            'email' => str_replace('"', '',$_POST['email']),
            'landline' => (int)$lnl,
            'gst' => $gst,
            'country_id' => str_replace('"', '',$_POST['country_id']),
            'state' => str_replace('"', '',$_POST['state']),
            'district' => str_replace('"', '',$_POST['district']),
            'city' => str_replace('"', '',$_POST['city']),
            'pincode' => str_replace('"', '',$_POST['pincode']),
            'address' => str_replace('"', '',$adr),
            'del' => '0',
            'date_created' => date('Y-m-d | h:i:sa')
        );
        if(isset($_POST['id'])){
            $this->db->where('id',$_POST['id']);
            $res = $this->db->update('whp_manufacturer',$data);
        }else{
            $res = $this->db->insert('whp_manufacturer', $data);
        }
        echo json_encode($res);
    }
    /**
     * Edit Data from this method.
     *
     * @return Response
     */
    public function edit()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        
        $manufacturer = $this->db->get_where('whp_manufacturer', array('id'=>$this->decrypt($data)))->result_array();
        
        echo json_encode($manufacturer);
    }
    
    public function update()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        $manufacturers=new ManufacturersModel;
        $manufacturers->update_manufacturer();
        return $manufacturers;
    }
    /**
     * Delete Data from this method.
     *
     * @return Response
     */
     public function delete()
    {
        // $products=new ProductsModel;
        // $products->delete_product($id);
        //redirect(base_url('products'));
        $data=json_decode(file_get_contents("php://input"),true);
        // echo "<pre>";
        // print_r($data);
        // exit;
        $res=$this->db->query("update whp_manufacturer set del=1 where id='".$data."'");
        echo json_decode($res);
    }

}
?>
