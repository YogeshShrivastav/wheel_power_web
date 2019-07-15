<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ManufacturerModel extends MY_Model
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
    public function get_manufacturer(){
        $this->db->select("whp_manufacturer.*,countries.name as country_name");
        $this->db->from("whp_manufacturer");
        $this->db->join('countries', 'countries.id = whp_manufacturer.country_id','left');
        if(isset($_POST["search"]["search_name"]))
        {
            $this->db->group_start();
            $this->db->like('whp_manufacturer.name',$_POST["search"]["search_name"]);
            $this->db->or_like('whp_manufacturer.mobile',$_POST["search"]["search_name"]);
            $this->db->or_like('whp_manufacturer.contact_person',$_POST["search"]["search_name"]);
            $this->db->group_end();
        }
        
        $this->db->where("whp_manufacturer.del","0");
        $this->db->order_by("whp_manufacturer.id","desc");
        $tmp_db=clone $this->db;
        $row=$tmp_db->get()->num_rows();
        
        $this->db->limit($_POST["pagelimit"],$_POST["start"]);
        $result=$this->db->get()->result_array();
        
        foreach ($result as $i => $v)
		{
            $this->db->select("whp_receive_item.id,count(whp_receive_item_detail.cat_no) as stock_count");
            $this->db->from('whp_receive_item');
            $this->db->where('whp_receive_item.vendor_id',$v['id']);
            $this->db->join("whp_receive_item_detail","whp_receive_item_detail.receive_item_id = whp_receive_item.id","left");
            $this->db->where("whp_receive_item.del","0");
            $stock =$this->db->get()->row_array();
            
            $result[$i]['incoming_stock_cn'] =  $stock['stock_count'];
            $result[$i]['ecrpt_id'] =  $this->encrypt($v["id"]);
		}
		
		$array=array("row_cn"=>$row,"data"=>$result);
		
        return $array;
    }
    
    public function get_manufacturer_list(){
        $this->db->select("*");
        $this->db->from("whp_manufacturer");
        $this->db->where("del","0");
        $this->db->order_by("name","asc");
        return $this->db->get()->result_array();
    }
    public function get_manufacturer_row()
    {
        $this->db->select("*");
        $this->db->from("whp_manufacturer");
        $this->db->where("del","0");
        return $this->db->get()->num_rows();
    }

    public function insert_manufacturer()
    {
        
        $data = array(
            'name' => $_POST('name'),
            'user_id' => $_POST('user_id'),
            'mobile' => $_POST('mobile'),
            'landline' => $_POST('landline'),
            'gst' => $_POST('gst'),
            'country_id' => $_POST('country_id'),
            'state' => $_POST('state'),
            'district' => $_POST('district'),
            'city' => $_POST('city'),
            'pincode' => $_POST('pincode'),
            'address' => $_POST('address'),
            'del' => '0',
            'date_created' => date('Y-m-d | h:i:sa')
        );
        return $this->db->insert('whp_manufacturer', $data);
    }


    public function update_manufacturer()
    {
        $data=array(
            'name' => $_POST('name'),
            'contact_person' => $_POST('contact_person'),
            'mobile' => $_POST('mobile'),
            'landline' => $_POST('landline'),
            'gst' => $_POST('gst'),
            'country_id' => $_POST('country_id'),
            'state' => $_POST('state'),
            'district' => $_POST('district'),
            'city' => $_POST('city'),
            'pincode' => $_POST('pincode'),
            'address' => $_POST('address'),
            'del' => '0',
            'date_created' => date('Y-m-d | h:i:sa')        
            );
        if($id==0){
            return $this->db->insert('whp_manufacturer',$data);
        }else{
            $this->db->where('id',$_POST('id'));
            return $this->db->update('whp_manufacturer',$data);
        }
    }

    public function delete_manufacturer($id){
        $data=array('del'=>'1');
        $this->db->where('id',$id)->update('whp_manufacturer',$data);
    }

}

?>