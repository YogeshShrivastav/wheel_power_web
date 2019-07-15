<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class loginModel extends MY_Model
{
    public function login()
    {
        $this->db->select('bch_all_loginusers.type_id as uid, bch_all_loginusers.type');
		$this->db->from('bch_all_loginusers');
		$this->db->where("bch_all_loginusers.username = '".$_POST['username']."' AND bch_all_loginusers.password = '".$_POST['password']."' ");
		$this->db->where("bch_all_loginusers.del = '0' ");
		$result = $this->db->get()->row_array();
		return $result;
    }
}

?>