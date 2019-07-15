<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-allow-origin:*');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');


class User extends MY_Controller {


    public function __construct() {
        parent:: __construct();
        $this->load->model('UserModel');
        $this->load->database();
    }

    //To User Login

    public function insert_user()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        // print_r($_POST);exit;
        
        $user=new UserModel;
        $data=$user->submit_user();
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
    
    public function get_all_user()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $user=new UserModel;
        $data=$user->getall_user();
        
        echo json_encode($data);
    }
    
    public function update_status()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $user=new UserModel;
        $data=$user->update_user_status();
        
        echo json_encode($data);
    }
    
    public function get_user_detail()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        $user=new UserModel;
        $data=$user->get_userdetail();
        echo json_encode($data);
    }
    
    public function update_user(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        $user=new UserModel;
        $data=$user->update_user1();
        echo json_encode($data); //print_r($_POST);
    }
}
    
