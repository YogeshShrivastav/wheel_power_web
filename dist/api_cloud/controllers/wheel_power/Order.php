<?php 
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include 'APP_Controller.php';
class Order extends APP_Controller 
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

    public function __construct() {
        parent:: __construct();
        // $this->load->helper("url");
        $this->load->library("pagination");
        $this->load->database();
    }

    //Order Tab Start
    
//     public  function  submit_order()
// 	{
// 		$data = json_decode(file_get_contents('php://input'),true);

// 		if($this->payload_val->{'id'})
// 		{

// 			$this->db->query("INSERT into abq_dr_order( date_created, created_by, created_by_type, dr_id, order_date ,sub_total , gst , gross_total ,balance , order_total ,order_process ,order_status,comment) values('".date('Y-m-d')."', '".$this->payload_val->{'id'}."','executive','".$this->payload_val->{'id'}."','".date('Y-m-d H:i:s')."','".$data[0]["sub_total"]."','".$data[0]["gst_value"]."','".$data[0]["gross_total"]."','".$data[0]["total_amount"]."','".$data[0]["total_amount"]."',0,'Pending','".$data[0]["comment"]."')");

// 			$odr_id = $this->db->insert_id();

// 			for($i = 0; $i < count($data);$i++)
// 			{

// 				$this->db->query("INSERT into abq_dr_order_item(order_id, product_id, dr_id, product_category, product_category_no, qty,rate, total) values('".$odr_id."', '".$data[$i]["id"]."', '".$this->payload_val->{'id'}."', '".$data[$i]["category"]."', '".$data[$i]['cat_no']."', '".$data[$i]["added_value"]."','".$data[$i]["price"]."','".$data[$i]["price_new"]."')");

// 			}

// 			$this->db->query("INSERT INTO `abq_dr_summary`(`date_created`,`created_by`,`dr_id`, `description`) VALUES ('".date('Y-m-d H:i:s')."','".$this->payload_val->{'id'}."','".$this->payload_val->{'id'}."','Order Created')");
// 		}

// 		echo json_encode($data);exit();
// 		echo json_encode("order successfully added");
// 	}

    public function order_list()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        $this->db->select('abq_dr_order.*,abq_dr.type,dr2.dr_name as created_by,abq_user.name as user_name, abq_dr.dr_name as counter_name');
        $this->db->from('abq_dr_order');
        $this->db->join('abq_dr','abq_dr.id = abq_dr_order.dr_id','left');
        $this->db->join('abq_dr as dr2','dr2.id = abq_dr_order.created_by','left');
        $this->db->join('abq_user','abq_user.id = abq_dr_order.created_by','left');
        $this->db->where("abq_dr_order.del", 0);
        $this->db->where("abq_dr.type", $_POST['type']);
        $this->db->where("abq_dr.del", '0');

        if($_SESSION['uaccess_level'] == 3) {
          $this->db->where("abq_dr.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = '".$_SESSION['uid']."')");
        }

        $this->db->where("abq_dr_order.order_process", 0);
        $this->db->order_by("abq_dr_order.id",'DESC');
        $orders_list = $this->db->get()->result();
        
        $cnt=0;
        foreach($orders_list as $key =>$row)
        {
            $arr[$cnt] = $row;
            $cnt = $cnt+1;

            $arr[$cnt] = array();
            //$arr[$cnt]['type']= $row->id; 
            $arr[$cnt]['detail']= 1;
            $arr[$cnt]['id']= $row->id;
            $arr[$cnt]['created_by']= $row->created_by;
            $arr[$cnt]['counter_name']= $row->counter_name;
            $arr[$cnt]['order_status']= $row->order_status;
            $arr[$cnt]['date_created']= $row->date_created;
            $arr[$cnt]['order_total']= $row->order_total;
            $cnt = $cnt+1;  
        }


        // $this->db->distinct();
        // $this->db->select('segment_name,segment_name as seg_name');
        // $this->db->from('abq_product');
        // $this->db->where("abq_product.del", '0');
        // $segment = $this->db->get()->result();

        // $catdata =[];$productdata =[];

        // foreach($segment as $key=>$row) 
        // {
        //   $catdataa = $this->db->query("SELECT product_category_no as cat_nos,product_category_no,segment_name as seg_name, product_name as product, id FROM abq_product where segment_name='".$row->segment_name."' order by id DESC")->result_array();

        //   foreach($catdataa as $key=>$cat)
        //   {
        //     $catdata [] = $cat;
        //     $productdataaa = $this->db->query(" SELECT product_name as product,product_category_no as cat_nos,segment_name as seg_name,id FROM abq_product where product_category_no='".$cat['product_category_no']."' and del=0 order by id DESC")->result_array();
        //     foreach($productdataaa as $prdct)
        //     {
        //       $productdata [] = $prdct;
        //     }
        //   }
          
        // }
        

        if($_SESSION['uaccess_level'] == 3) {

            $this->db->select('(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and s.type="'.$_POST['type'].'" and c.order_process = 0 and c.del=0 and c.order_status = "Pending") as pend_order,(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and s.type="'.$_POST['type'].'" and c.order_process = 0 and c.del=0 and c.order_status = "Approved") as app_order,(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and s.type="'.$_POST['type'].'" and  c.order_process = 0 and c.del=0 and c.order_status = "Reject") as rej_order,(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and s.type="'.$_POST['type'].'" and  c.order_process = 0 and c.del=0 and c.order_status = "Complete") as cmplt_order');

        } else {

          $this->db->select('(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.type="'.$_POST['type'].'" and c.order_process = 0 and c.del=0 and c.order_status = "Pending") as pend_order,(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.type="'.$_POST['type'].'" and c.order_process = 0 and c.del=0 and c.order_status = "Approved") as app_order,(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.type="'.$_POST['type'].'" and  c.order_process = 0 and c.del=0 and c.order_status = "Reject") as rej_order,(select COUNT(c.id) from abq_dr_order c LEFT JOIN abq_dr s on s.id = c.dr_id where s.del=0 and s.type="'.$_POST['type'].'" and  c.order_process = 0 and c.del=0 and c.order_status = "Complete") as cmplt_order');


        }
       
      // $this->db->join('abq_dr','abq_dr.id = abq_dr_order.dr_id','left');
    //     $this->db->where("abq_dr.type", $_POST['type']);
        $a1=$this->db->get()->row();

    $result = array('orders_list' => $arr, /*'segment' => $segment,*/ 'notifydata' => $a1,/*'category' => $catdata,'product' => $productdata*/);

        echo json_encode($result);
    }


    public function orderdetail($odr_id)
    {
        $orderstatus = $this->db->query("select order_status,dr_id from abq_dr_order where id='".$odr_id."' ")->row_array();
        
        $drdatatype=$this->db->query("select type from abq_dr where id='".$orderstatus['dr_id']."'")->row_array();

        $order_status = $orderstatus['order_status'];

        $result = $this->db->query("select abq_order_segment_delivery.* from abq_order_segment_delivery LEFT JOIN abq_dr_order ON abq_order_segment_delivery.order_id = abq_dr_order.id  where order_id='".$odr_id."' AND abq_dr_order.order_process='0' ")->result_array();

        $distr_cmpny=[];
        $tempdistr_cmpny=[];
        
        $segment=[];
        $product=[];
        $i=0;
        $j=0;
        $k=0;

        foreach($result as $key=>$row)
        {
            if($drdatatype['type']==1)
            {
                $dr_name=$this->db->query("select dr_name from abq_dr where id='".$row['segment_delivery']."'")->row_array();
                $rut=0; 
                 foreach($tempdistr_cmpny as $key=>$row1)
                 {
                      if ($row1['id'] === $row['segment_delivery']) 
                      {
                             $rut = 1;
                      }
                 }
                if($rut==0)
                {
                    $tempdistr_cmpny[$j]['name'] = $dr_name['dr_name'];
                    $tempdistr_cmpny[$j]['id']=$row['segment_delivery'];
                    $j++;
                }
            }
            else if($drdatatype['type']==2)
            {
                
                 $rut=0; 
                 foreach($tempdistr_cmpny as $key=>$row1)
                 {
                      if ($row1['name'] === $row['segment_delivery']) 
                      {
                             $rut = 1;
                      }
                 }
                if($rut==0)
                {
                    $tempdistr_cmpny[$j]['name']=$row['segment_delivery'];
                    $j++;
                }
            }
        }

        
        $distr_cmpny= $tempdistr_cmpny;
        foreach($result as $row)  
        {
            
            if(is_numeric($row['segment_delivery']))
            {
                 $dis_amt = ($row['segment_total'] * $row['segment_discount'])/100;
    
                $segment[$i]=$row;
                $segment[$i]['segment_discount']=$dis_amt;

                $product_data=$this->db->query("select * from abq_dr_order_item where segment_name='".$row['segment_name']."' and order_id='".$odr_id."' ")->result_array();

                foreach($product_data as $pro)
                {
                        $product[$k]=$pro;
                        $k++;
                }
                $i++;
            }
            else
            {

                $product_data=$this->db->query("select * from abq_dr_order_item where segment_name='".$row['segment_name']."' and order_id='".$odr_id."' ")->result_array();

                foreach($product_data as $pro)
                {
                        $product[$k]=$pro;
                        $k++;
                }

                 $dis_amt = ($row['segment_total'] * $row['segment_discount'])/100;

                $segment[$i]=$row;
                $segment[$i]['segment_discount']=$dis_amt;
                $i++;
            }

            $j++;
        }

        $result = array('segmentData' => $segment, 'DistData' => $distr_cmpny, 'order_status' => $order_status, 'product' => $product);
        echo json_encode($result);
    }

    public function submit_status($odr_id,$status,$reason)
    {
        $odrdata=$this->db->query("select order_status,dr_id,order_total from abq_dr_order where id='".$odr_id."'")->row_array();
        if($odrdata['order_status']!=$status)
        {    
            if($status=='Approved')
            {
            
              $disdata=$this->db->query("select segment_delivery from abq_order_segment_delivery where order_id='".$odr_id."' and segment_status!='Approved'")->result_array();
             
              $distcmpny_id=[];
              $k=0;
                
              foreach($disdata as $data)
              {
                  $distcmpny_id[$k] = $data['segment_delivery'];  
                  $k++;
              }
              
              $unique_distcmpnytemp = array_unique($distcmpny_id);
              $unique_distcmpny=[];

              foreach($unique_distcmpnytemp as $datatemp)
              {
                  $unique_distcmpny[] =  $datatemp;
              }

              $drdata=$this->db->query("select dr_id from abq_dr_order where id='".$odr_id."'")->row_array();
              $drtypedata = $this->db->query("select type from abq_dr where id='".$drdata['dr_id']."'")->row_array();
            
              $total_order_value=0;
               
                
              for($i=0;$i<sizeof($unique_distcmpny);$i++)
              {
                $result=$this->db->query("select * from abq_order_segment_delivery where order_id='".$odr_id."' and segment_delivery='".$unique_distcmpny[$i]."' and segment_status!='Approved'")->result_array();
              
      
                $order_total=0;
                $disname=0;
                $distid=0;
                $odrid=0;
                $odrvalue=0;
                $odrgst=0;
                $cmpnyname='';
      
                foreach($result as $row1)
                {
    
                    $odrvalue= $odrvalue + $row1['segment_total'];
                    $order_total= $order_total + $row1['segment_total_amount'];
                    $odrgst= $odrgst + $row1['gst_amount'];
                    if($drtypedata['type']==1)
                    {
                        $dist_name= $this->db->query("select dr_name from abq_dr where id='".$row1['segment_delivery']."' and type = 2")->row_array();
    
                        $disname = $dist_name['dr_name'];
                        $distid = $row1['segment_delivery'] ;

                        $this->db->query("update abq_dr_order_item set distributor_id='".$distid."' where segment_name='".$row1['segment_name']."' and order_id='".$odr_id."'");

                        $this->db->query("UPDATE abq_order_segment_delivery SET segment_status='Approved' where segment_delivery='".$row1['segment_delivery']."' and order_id='".$odr_id."'");

                    }
                    else if($drtypedata['type']==2)
                    {
                        $cmpnyname = $row1['segment_delivery'];
                        $this->db->query("UPDATE abq_order_segment_delivery SET segment_status='Approved' where segment_delivery='".$row1['segment_delivery']."' and order_id='".$odr_id."'");
                    }
                
                    $odrid = $row1['order_id'];
                }
      
                // if($drtypedata['type']==1)
                // {
                    
                //   $prvbaldata= $this->db->query("select balance from abq_dr_order_payment_child where retailer_id='".$drdata['dr_id']."' && distributor_id='".$distid."' order by id desc limit 1")->row_array();
                  
                //    if(isset($prvbaldata['balance'])){
                //       $newamuntbal= $order_total + $prvbaldata['balance'];
                //    }else{
                //       $newamuntbal= $order_total;
                //    }

                //   $this->db->query("INSERT into abq_dr_order_payment_child(date_created,created_by,retailer_id, distributor_id,order_id,order_amount,balance) values('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."', '".$drdata['dr_id']."','".$distid."','".$odrid."','".$order_total."','".$newamuntbal."')");
                // }
                  
                // else if($drtypedata['type']==2)
                // {
                      
                //   $prvbaldata= $this->db->query("select balance from abq_dr_order_payment_child where distributor_id='".$drdata['dr_id']."' && company_name='".$cmpnyname."' order by id desc limit 1")->row_array();
                    
                //   if(isset($prvbaldata['balance'])){
                //       $newamuntbal= $order_total + $prvbaldata['balance'];
                //   }else{
                //       $newamuntbal= $order_total;
                //   }
                    
                //   $this->db->query("INSERT into abq_dr_order_payment_child(date_created,created_by,distributor_id, company_name,order_id,order_amount,balance) values('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."', '".$drdata['dr_id']."','".$cmpnyname."','".$odrid."','".$order_total."','".$newamuntbal."')");
                // }
                $total_order_value = $total_order_value + $order_total;
              }
                
              $this->db->query("update abq_dr_order set order_status='".$status."',approved_by='".$_SESSION['uid']."' where id='".$odr_id."' ");
                
              $this->db->query("INSERT INTO `abq_dr_summary`(`date_created`,`created_by`,`dr_id`, `description`) VALUES ('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."','".$drdata['dr_id']."','Order Approved (Order No. $odr_id)')");
            }
            else
            {
                $disdata=$this->db->query("select segment_delivery from abq_order_segment_delivery where order_id='".$odr_id."'")->result_array();
             
                $distcmpny_id=[];
                $k=0;
                
                foreach($disdata as $data)
                {
                    $distcmpny_id[$k] = $data['segment_delivery'];  
                    $k++;
                }
                $unique_distcmpnytemp= array_unique($distcmpny_id);

                foreach($unique_distcmpnytemp as $datatemp)
                {
                    $unique_distcmpny[] =  $datatemp;
                }

                $drdata=$this->db->query("select dr_id from abq_dr_order where id='".$odr_id."'")->row_array();
                $drtypedata = $this->db->query("select type from abq_dr where id='".$drdata['dr_id']."'")->row_array();
                
               for($i=0;$i<sizeof($unique_distcmpny);$i++)
               {
                    $result=$this->db->query("select * from abq_order_segment_delivery where order_id='".$odr_id."' and segment_delivery='".$unique_distcmpny[$i]."'")->result_array();
        
                    foreach($result as $row1)
                    {
                        if($drtypedata['type']==1)
                        {

                          $this->db->query("UPDATE abq_order_segment_delivery SET segment_status='".$status."',segment_reason_reject='".$reason."' where segment_delivery='".$row1['segment_delivery']."' and order_id='".$odr_id."'");
                        }
                    }
                }
                
                $this->db->query("update abq_dr_order set order_status='".$status."', approved_by='".$_SESSION['uid']."', reason_reject='".@$reason."' where id='".$odr_id."' ");
              
               
                $this->db->query("INSERT INTO `abq_dr_summary`(`date_created`,`created_by`,`dr_id`, `description`) VALUES ('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."','".$drdata['dr_id']."','Order $status (Order No. $odr_id)')");
            }
        } 

        // msgs code start

          
          /*if($status=='Approved')
          {
            $this->load->library('curl');
            $drmsg_data = $this->db->query("select abq_dr.type,abq_dr.dr_name,abq_dr_contact.contact_1,abq_dr.id from abq_dr left join abq_dr_contact on abq_dr.id = abq_dr_contact.dr_id  where abq_dr.id='".$odrdata['dr_id']."' ")->row_array();
            $seg_data=$this->db->query("select segment_name,segment_total_amount,segment_delivery from abq_order_segment_delivery where order_id='".$odr_id."' ")->result_array();

            $k=0;
            foreach($seg_data as $data)
            {
              $distcmpny_id[$k] = $data['segment_delivery'];  
              $k++;
            }
        

            $unique_distcmpnytemp= array_unique($distcmpny_id);

    
            foreach($unique_distcmpnytemp as $datatemp)
            {
              $unique_distcmpny[] =  $datatemp;
            }

            $ret_seg_string = '';
            $dist_seg_string = '';
            foreach($seg_data as $row)
            {
                
              if($drmsg_data['type']==1)
              {      
                $dist_dr_data =$this->db->query("select dr_name from abq_dr where id='".$row['segment_delivery']."' and del=0 and type=2")->row_array();
           
                $ret_seg_string.=$row['segment_name'].'-'.$row['segment_total_amount'] .'-'. $dist_dr_data['dr_name'];
              }
              else
              {
                $ret_seg_string.=$row['segment_name'].'-'.$row['segment_total_amount'] .'-'. $row['segment_delivery'];
              }
            }

            
            if($drmsg_data['contact_1'])
            { 

              

              $retmsgstring = urlencode("".$drmsg_data['dr_name']." your order has been approved by Prayag Admin and order details are mentioned as
Order Reference No #ORD".$odr_id.".
".$ret_seg_string."");

              $msg="https://www.smsjust.com/blank/sms/user/urlsms.php?username=prayagindia&pass=d7b-UQ$8&senderid=PRAYAG&dest_mobileno=".$drmsg_data['contact_1']."&msgtype=UNI&message=".$retmsgstring."&response=Y";
            echo $msg;
              $sendsms = $this->curl->simple_get($msg);
            }

            for($i=0;$i<sizeof($unique_distcmpny);$i++)
            {
                $dissegm=$this->db->query("select segment_name,segment_total_amount from abq_order_segment_delivery where segment_delivery='".$unique_distcmpny[$i]."' and order_id='".$odr_id."'")->result_array();
                foreach($dissegm as $ds)
                {
                  $dist_seg_string.=$ds['segment_name'].'-'.$ds['segment_total_amount'];
                }
        
                $disttt_dr_data =$this->db->query("select abq_dr.dr_name,abq_dr_contact.contact_1 from abq_dr left join abq_dr_contact on abq_dr_contact.dr_id = abq_dr.id where abq_dr.id='".$unique_distcmpny[$i]."' and abq_dr.del=0 and abq_dr.type=2")->row_array();

                if($disttt_dr_data['contact_1'] && $drmsg_data['type']==1 )
                {



                    $distmsgstring = urlencode("".$drmsg_data['dr_name']." Order has been Approved by Prayag Admin and order details are mentioned as Order Reference No. #ORD".$odr_id.". ".$dist_seg_string."");

                    $msg1="https://www.smsjust.com/blank/sms/user/urlsms.php?username=prayagindia&pass=d7b-UQ$8&senderid=PRAYAG&dest_mobileno=".$disttt_dr_data['contact_1']."&msgtype=UNI&message=".$distmsgstring."&response=Y";

                    echo $msg1;
                    $sendsms1 = $this->curl->simple_get($msg1);
                }
            }

            $salesdata=$this->db->query("select abq_user.contact_01 from abq_dr_assign left join abq_user on abq_user.id = abq_dr_assign.assigned_to where abq_dr_assign.dr_id='".$drmsg_data['id']."'")->result_array();

            foreach($salesdata as $s)
            {
              if($s['contact_01'])
              {
                  $salemsgstring = urlencode("".$drmsg_data['dr_name']." Order has been Approved by Prayag Admin and now total Secondary Sales of this counter is ".$odrdata['order_total']." and approved order details are mentioned as Order Reference No. #ORD".$odr_id.".".$dist_seg_string."");

                  $msg2="https://www.smsjust.com/blank/sms/user/urlsms.php?username=prayagindia&pass=d7b-UQ$8&senderid=PRAYAG&dest_mobileno=".$s['contact_01']."&msgtype=UNI&message=".$salemsgstring."&response=Y";

                  echo $msg2;

                  $sendsms2 = $this->curl->simple_get($msg2);
              }
            } 

            $sales_statedata=$this->db->query("select abq_user.contact_01 from abq_dr join abq_sa_states on abq_dr.state_name = abq_sa_states.state_name join abq_user on abq_user.id = abq_sa_states.assigned_to where abq_dr.id='".$drmsg_data['id']."'")->row_array();
            if($sales_statedata['contact_01'])
            {
              

                  $salemsgstring = urlencode("".$drmsg_data['dr_name']." Order has been Approved by Prayag Admin and now total Secondary Sales of this counter is ".$odrdata['order_total']." and approved order details are mentioned as Order Reference No. #ORD".$odr_id.".".$dist_seg_string."");

                  $msg3="https://www.smsjust.com/blank/sms/user/urlsms.php?username=prayagindia&pass=d7b-UQ$8&senderid=PRAYAG&dest_mobileno=".$sales_statedata['contact_01']."&msgtype=UNI&message=".$salemsgstring."&response=Y";

                  echo $msg3;

                  $sendsms3 = $this->curl->simple_get($msg3);
            }
          }  */

      //msgs code end   
    }

    public function getallret_dist($type)
    {
        $this->db->distinct();
        $this->db->select('*');
        $this->db->from('abq_dr');
        $this->db->where("del", 0);
        $this->db->where("type", $type);

        if($_SESSION['uaccess_level'] == 3) {
           $this->db->where("state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = '".$_SESSION['uid']."')");
        }

        $data = $this->db->get()->result();
        echo json_encode($data);
    }
    
    public function get_retdisaddress($dr)
    {
        $this->db->distinct();
        $this->db->select('abq_dr.street,abq_dr_contact.contact_1,abq_dr_contact.contact_person,abq_dr.state_name,abq_dr.pincode');
        $this->db->from('abq_dr');
        $this->db->join('abq_dr_contact','abq_dr_contact.dr_id = abq_dr.id','left');
        $this->db->where("abq_dr.del", 0);
        $this->db->where("abq_dr.id", $dr);
        $data1 = $this->db->get()->row();
        
        $this->db->select('abq_dr_segment.segment_name,abq_dr_segment.segment_name as seg_name,abq_dr_segment.seg_dis');
        $this->db->from('abq_dr_segment');
        $this->db->where("abq_dr_segment.dr_id", $dr);
        $this->db->where("abq_dr_segment.del", 0);
        $this->db->where("abq_dr_segment.segment_name!=''");
        $this->db->group_by('abq_dr_segment.segment_name');
        $data2 = $this->db->get()->result();

        $this->db->select('abq_dr_segment.brand_name');
        $this->db->from('abq_dr_segment');
        $this->db->where("abq_dr_segment.dr_id", $dr);
        $this->db->where("abq_dr_segment.del", 0);
        $this->db->where("abq_dr_segment.segment_name!=''");
        $this->db->group_by('abq_dr_segment.brand_name');
        $brand_data = $this->db->get()->result();

        $seriesdata=[];$catdata =[];$productdata =[];

        foreach($brand_data as $rowbrand) 
        {

          foreach($data2 as $row) 
          {
            $series_data = $this->db->query("SELECT TRIM(LEADING ' ' FROM series) as series,abq_product.segment_name as seg_name,abq_product.brand_name FROM abq_product join abq_dr_segment on abq_dr_segment.brand_name = abq_product.brand_name and abq_dr_segment.segment_name = abq_product.segment_name where abq_dr_segment.dr_id='".$dr."' and abq_product.brand_name='".$rowbrand->brand_name."' and abq_product.segment_name='".$row->segment_name."' and abq_product.del='0' group by abq_product.series order by abq_product.id DESC")->result_array();

            foreach($series_data as $series)
            {

              if(sizeof($seriesdata)>0)
              {
                $search=null;
                foreach ($seriesdata as $key => $val) 
                {
                          if ($val['series'] === $series['series'] && $val['seg_name']===$series['seg_name']) 
                          {
                              $search=$key;
                          }
                      }

                      if($search===null)
                      {
                        $seriesdata [] = $series;
                      }
                  }
                  else
                  {
                    $seriesdata [] = $series;
                  }

              $product_data = $this->db->query(" SELECT series,TRIM(LEADING ' ' FROM product_name)  as product,product_category_no as cat_nos,segment_name as seg_name,id,brand_name FROM abq_product where series='".$series['series']."' and del= '0' group by product_name order by id DESC")->result_array();
              foreach($product_data as $prdct)
              {
                if(sizeof($productdata)>0)
                {
                  $search=null;
                  foreach ($productdata as $key => $val) 
                  {
                            if ($val['product'] === $prdct['product'] && $val['seg_name']===$prdct['seg_name'] && $val['series']===$prdct['series']) 
                            {
                                $search=$key;
                            }
                        }

                        if($search===null)
                        {
                          $productdata [] = $prdct;
                        }
                    }
                    else
                    {
                      $productdata [] = $prdct;
                    }
                
                $cat_data = $this->db->query("SELECT series,product_category_no as cat_nos,product_category_no,segment_name as seg_name, product_name as product, id,brand_name FROM abq_product where brand_name='".$rowbrand->brand_name."' and segment_name='".$row->segment_name."' and series='".$series['series']."' and product_name='".$prdct['product']."' and del='0' order by id DESC")->result_array();
                foreach($cat_data as $cat)
                {
                  if(sizeof($catdata)>0)
                  {
                    $search=null;
                    foreach ($catdata as $key => $val) 
                    {
                              if ($val['cat_nos'] === $cat['cat_nos']) 
                              {
                                  $search=$key;
                              }
                          }

                          if($search===null)
                          {
                            $catdata [] = $cat;
                          }
                      }
                      else
                      {
                        $catdata [] = $cat;
                      }
                  
                }
              }
            }
          }
        }
        
        $result = array('info' => $data1, 'dr_segment' => $data2,'category' => $catdata, 'product' => $productdata,'seriesdata' => $seriesdata,'brand_data'=>$brand_data);
        echo json_encode($result);
    }

    //Order Tab End



    // Payment Tab Start

    public function payment_list()
    {
        
        $_POST = json_decode(file_get_contents('php://input'), true);

        if($_POST['type']==2)
        {
            $type="abq_dr_payment.type='Distributor'";
            $count_type = "c.type='Distributor'";
        }
        elseif($_POST['type']==1)
        {

            $type="abq_dr_payment.type='Company'";
            //$count_type = "c.type='Exim' or c.type='Polymer'";
            $count_type = "c.type='Company'";
        }

        $this->db->select('abq_dr_payment.*,abq_dr.type,dr2.dr_name as created_by, abq_dr.dr_name as counter_name,abq_user.name as user_name,dr1.dr_name as retdis_countername');
        $this->db->from('abq_dr_payment');
        $this->db->join('abq_dr','abq_dr.id = abq_dr_payment.type_id_name','left');
        $this->db->join('abq_dr as dr1','dr1.id = abq_dr_payment.dr_id','left');
        $this->db->join('abq_dr as dr2','dr2.id = abq_dr_payment.created_by','left');
        $this->db->join('abq_user','abq_user.id = abq_dr_payment.created_by','left');
        $this->db->where("abq_dr_payment.del", 0);

        if($_SESSION['uaccess_level'] == 3) {
           $this->db->where("dr1.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = '".$_SESSION['uid']."')");
        }

        $this->db->where("($type)");
        $this->db->order_by("abq_dr_payment.id",'DESC');
        //$this->db->where("abq_dr_order_payment_child.balance!=", 0);
        $payment_list=$this->db->get()->result();

         
        if($_POST['type']==2)
        {

            if($_SESSION['uaccess_level'] == 3) {

                $this->db->select('(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name LEFT JOIN abq_dr as dr2 on dr2.id = c.dr_id where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "pending" and dr2.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'")) as pend_pay,(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name LEFT JOIN abq_dr as dr2 on dr2.id = c.dr_id where s.del=0 and dr2.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and c.del=0 and '.$count_type.' and c.payment_status = "Verified") as verfy_pay');
                $a1=$this->db->get()->row();

            } else {

               $this->db->select('(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "pending") as pend_pay,(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "Verified") as verfy_pay');
                $a1=$this->db->get()->row();

            }

        } else if($_POST['type']==1) {

             if($_SESSION['uaccess_level'] == 3) {

                 $this->db->select('(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.dr_id where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and s.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and c.del=0 and '.$count_type.' and c.payment_status = "pending") as pend_pay,(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.dr_id where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and s.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and c.del=0 and '.$count_type.' and c.payment_status = "Verified") as verfy_pay');
                 $a1=$this->db->get()->row();

            } else {

               $this->db->select('(select COUNT(c.id) from abq_dr_payment c where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and c.del=0 and '.$count_type.' and c.payment_status = "pending") as pend_pay,(select COUNT(c.id) from abq_dr_payment c where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and c.del=0 and '.$count_type.' and c.payment_status = "Verified") as verfy_pay');
               $a1=$this->db->get()->row();
           }
        }

        $result = array('payment_list' => $payment_list, 'notifydata' => $a1);

        echo json_encode($result);  

    }

    public function verify_pay_status($dr_id,$id)
    {
        //$data = json_decode(file_get_contents('php://input'), true);

        $this->db->query("update abq_dr_payment set payment_status='Verified' where id='".$id."' ");
        
        $this->db->query("INSERT INTO `abq_dr_summary`(`date_created`,`created_by`,`dr_id`, `description`) VALUES ('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."','".$dr_id."','Payment Verified')");
        
        $dist_cmpnydata=$this->db->query("select order_id,amount,type_id_name from abq_dr_payment where id='".$id."'")->row_array();
        
        $drdata = $this->db->query("select type from abq_dr where id='".$dr_id."'")->row_array();
        
        if($drdata['type']==1)
        {

            $prv_bal=$this->db->query("select balance from abq_dr_order_payment_child where retailer_id='".$dr_id."' and distributor_id='".$dist_cmpnydata['type_id_name']."' order by id desc")->row_array();

            $new_bal = $prv_bal['balance'] - $dist_cmpnydata['amount'];

            $this->db->query("INSERT into abq_dr_order_payment_child(date_created,created_by,retailer_id,distributor_id,payment_id,payment_amount,balance) values('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."','".$dr_id."','".$dist_cmpnydata['type_id_name']."','".$id."','".$dist_cmpnydata['amount']."','".$new_bal."')");
        }
        else if($drdata['type']==2)
        {
            $prv_bal=$this->db->query("select balance from abq_dr_order_payment_child where distributor_id='".$dr_id."' and company_name='".$dist_cmpnydata['type_id_name']."' order by id desc")->row_array();

            $new_bal = $prv_bal['balance'] - $dist_cmpnydata['amount'];

            $this->db->query("INSERT into abq_dr_order_payment_child(date_created,created_by,distributor_id,company_name,payment_id,payment_amount,balance) values('".date('Y-m-d H:i:s')."','".$_SESSION['uid']."','".$dr_id."','".$dist_cmpnydata['type_id_name']."','".$id."','".$dist_cmpnydata['amount']."','".$new_bal."')");
        }
        
        if($drdata['type']==1)
        {
            $type="abq_dr_payment.type='Distributor'";
            $count_type = "c.type='Distributor'";
        }
        elseif($drdata['type']==2)
        {

            $type="abq_dr_payment.type='Company'";
            $count_type = "c.type='Company'";
        }

        $this->db->select('abq_dr_payment.*,abq_dr.type,dr2.dr_name as created_by, abq_dr.dr_name as counter_name,abq_user.name as user_name,dr1.dr_name as retdis_countername');
        $this->db->from('abq_dr_payment');
        $this->db->join('abq_dr','abq_dr.id = abq_dr_payment.type_id_name','left');
        $this->db->join('abq_dr as dr1','dr1.id = abq_dr_payment.dr_id','left');
        $this->db->join('abq_dr as dr2','dr2.id = abq_dr_payment.created_by','left');
        $this->db->join('abq_user','abq_user.id = abq_dr_payment.created_by','left');
        $this->db->where("abq_dr_payment.del", 0);
        $this->db->where("abq_dr_payment.id", $id);
        $this->db->where("($type)");
        $data=$this->db->get()->row();  
        
        if($drdata['type']==1)
        {

            if($_SESSION['uaccess_level'] == 3) {

                  $this->db->select('(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name LEFT JOIN abq_dr as dr2 on dr2.id = c.dr_id where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "pending" and dr2.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'")) as pend_pay,(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name LEFT JOIN abq_dr as dr2 on dr2.id = c.dr_id where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "Verified" and dr2.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'")) as verfy_pay');
                  $a1=$this->db->get()->row();

            } else {

                  $this->db->select('(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "pending") as pend_pay,(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr s on s.id = c.type_id_name where s.del=0 and c.del=0 and '.$count_type.' and c.payment_status = "Verified") as verfy_pay');
                  $a1=$this->db->get()->row();
            }

        }
        elseif($drdata['type']==2)
        {

            if($_SESSION['uaccess_level'] == 3) {

                $this->db->select('(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr as dr on dr.id = c.dr_id where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and c.del=0 and '.$count_type.' and c.payment_status = "pending" and dr.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'")) as pend_pay,(select COUNT(c.id) from abq_dr_payment c LEFT JOIN abq_dr as dr on dr.id = c.dr_id where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and c.del=0 and '.$count_type.' and dr.state_name in (SELECT state_name FROM abq_sa_states WHERE assigned_to = "'.$_SESSION['uid'].'") and c.payment_status = "Verified") as verfy_pay');
                $a1=$this->db->get()->row();

            } else {

                $this->db->select('(select COUNT(c.id) from abq_dr_payment c where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and c.del=0 and '.$count_type.' and c.payment_status = "pending") as pend_pay,(select COUNT(c.id) from abq_dr_payment c where (c.type_id_name="Polymer" ||  c.type_id_name="Exim") and c.del=0 and '.$count_type.' and c.payment_status = "Verified") as verfy_pay');
                $a1=$this->db->get()->row();
            }
        }
        
        $result = array('payment' => $data, 'notifydata' => $a1);
        
        echo json_encode($result);  
        //echo json_encode($data);
    }

    // Payment Tab End
    
    
    ///////////////////////////////////////// MY CODE //////////////////////////////////////////
    
    
     public function get_state(){
        
        $this->db->select('state_name');
        $this->db->from("whp_postal_master");
        $this->db->where("del","0");
        $this->db->group_by('state_name');
        $query = $this->db->get()->result_array();
        
        echo json_encode($query);
    }
    public function get_district(){
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $this->db->DISTINCT();
        $query = $this->db->select('district_name');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state_name']);
        $query = $this->db->order_by("district_name");
        $query = $this->db->get()->result_array();
        
        echo json_encode($query);
    }

    public function get_city(){
       
        $_POST=json_decode(file_get_contents("php://input"),true);
        // print_r($_POST);
        $this->db->DISTINCT();
        $query = $this->db->select('city');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state']);
        $query = $this->db->where('whp_postal_master.district_name',$_POST['district']);
        $query = $this->db->order_by("city");
        $query = $this->db->get()->result_array();
        
        echo json_encode($query);
    }
    
    public function get_area(){
       
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $this->db->DISTINCT();
        $query = $this->db->select('area');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state']);
        $query = $this->db->where('whp_postal_master.district_name',$_POST['district']);
        $query = $this->db->where('whp_postal_master.city',$_POST['city']);
        $query = $this->db->order_by("area");
        $query = $this->db->get()->result_array();
        
        echo json_encode($query);
    }
    public function get_pincode(){
       
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        $query = $this->db->select('pincode');
        $query = $this->db->from("whp_postal_master");
        $query = $this->db->where('whp_postal_master.state_name',$_POST['state']);
        $query = $this->db->where('whp_postal_master.district_name',$_POST['district']);
        $query = $this->db->where('whp_postal_master.city',$_POST['city']);
        $query = $this->db->where('whp_postal_master.area',$_POST['area']);
        $query = $this->db->get()->row_array();
        
        echo json_encode($query);
    }
    
    public function submit_order()
    {
        $_POST=json_decode(file_get_contents("php://input"),true);
        
        // print_r($_POST);
        // exit;
        
        $date_created = date('Y-m-d H:i:s');
        
        if(isset($_POST['mobile_2']) && $_POST['mobile_2'])
        {  $mobile_2 = $_POST['mobile_2'];  }
        else
        { $mobile_2 = ""; }
        
        if(isset($_POST['email']) && $_POST['email'])
        {  $email = $_POST['email'];  }
        else
        { $email = ""; }
       
        $exist_customer = $this->db->select("id")->from("whp_customer")->where("mobile_1",$_POST['mobile_1'])->where("del",'0')->get()->row_array();
        
        if(isset($exist_customer['id']) && $exist_customer['id'])
        {
            $customer_id = $exist_customer['id'];
        }
        else
        {
            $user_data = array(
                'date_created' => $date_created,
                'user_id' => $_POST['user_id'],
                'name' => $_POST['name'],
                'mobile_1' => $_POST['mobile_1'],
                'mobile_2' => $mobile_2,
                'email' => $email,
                'address' => $_POST['address'],
                'state' => $_POST['state'],
                'district' => $_POST['district'],
                'city' => $_POST['city'],
                'pincode' => $_POST['pincode'],
                );
            $this->db->insert("whp_customer",$user_data);
            
            $customer_id = $this->db->insert_id();
        }
      
        $order = array(
            'date_created' => $date_created,
            'user_id' => $_POST['user_id'],
            'total_item' => COUNT($_POST['order']),
            'order_total' => $_POST['order_total_amount'],
            'payment_total' => $_POST['total_payment'],
            'customer_id' => $customer_id,
            'status' => 'pending'
            );
        
        $this->db->insert("whp_order",$order);
        
        $order_id = $this->db->insert_id();
        
        
        for($i=0; $i<COUNT($_POST['order']); $i++)
        {
            $order_item = array(
                'date_created' => $date_created,
                'order_id' => $order_id,
                'brand' => $_POST['order'][$i]['brand'],
                'category' => $_POST['order'][$i]['category'],
                'sub_category' => $_POST['order'][$i]['sub_category'],
                'product_name' => $_POST['order'][$i]['product_name'],
                'cat_no' => $_POST['order'][$i]['cat_no'],
                'price' => $_POST['order'][$i]['price'],
                'qty' => $_POST['order'][$i]['qty']
                );
                
            $this->db->insert("whp_order_item",$order_item);
        }
        
        
        if(isset($_POST['cheque']) && $_POST['cheque'] == 1)
        {
            $this->db->query("insert into whp_order_payment_mode (date_created,order_id,mode,bank,cheque_no,cheque_date,amount) values ('".$date_created."', '".$order_id."', 'cheque', '".$_POST['cheque_data']['bank']."', '".$_POST['cheque_data']['cheque_no']."', '".$_POST['cheque_data']['cheque_date']."', '".$_POST['cheque_data']['cheque_amount']."' )");
        }
        
        if(isset($_POST['card']) && $_POST['card'] == 1)
        {
            $this->db->query("insert into whp_order_payment_mode (date_created,order_id,mode,trans_id,amount) values ('".$date_created."', '".$order_id."', 'card', '".$_POST['card_data']['trans_id']."', '".$_POST['card_data']['trans_amount']."' )");
        }
        
        if(isset($_POST['cash']) && $_POST['cash'] == 1)
        {
            $this->db->query("insert into whp_order_payment_mode (date_created,order_id,mode,amount) values ('".$date_created."', '".$order_id."', 'cash', '".$_POST['cash_data']['cash_amount']."' )");
        }
        
        if(isset($_POST['credit']) && $_POST['credit'] == 1)
        {
            $this->db->query("insert into whp_order_credit_payment (date_created,order_id,no_of_days,credit_amount,status) values ('".$date_created."', '".$order_id."', '".$_POST['credit_data']['no_of_days']."', '".$_POST['credit_data']['credit_amt']."', 'pending' )");
        }
        
        echo json_encode(array('order_id' => $order_id,'msg'=>'success'));
    }
    
    public function get_order()
    {
        
        $_POST = json_decode(file_get_contents("php://input"),true);
        
        $this->db->select("whp_order.*,whp_user.name as created_by_name,whp_customer.name as customer_name,whp_order_credit_payment.credit_amount");
        $this->db->from("whp_order");
        $this->db->join("whp_user","whp_order.user_id = whp_user.id","left");
        $this->db->join("whp_customer","whp_order.customer_id = whp_customer.id","left");
        $this->db->join("whp_order_credit_payment","whp_order.id = whp_order_credit_payment.order_id AND whp_order_credit_payment.status = 'pending' ","left");
        $this->db->where("whp_order.user_id",$_POST['user_id']);
        if(isset($_POST['search']['search_val']) && $_POST['search']['search_val'])
        {
            $this->db->group_start();
            $this->db->like("whp_customer.name",$_POST['search']['search_val']);
            $this->db->group_end();
        }
        $this->db->where("whp_order.del","0");
        $this->db->order_by("whp_order.id","desc");
        // $this->db->group_by("customer_id");
        $order = $this->db->get()->result_array();
        // print_r($order);
        $i=0;
        foreach($order as $val)
        {
            // print_r($val);
            $this->db->select("*");
            $this->db->from("whp_order_item");
            $this->db->where("order_id",$val['id']);
            $this->db->where("del","0");
            $item = $this->db->get()->result_array();
            
            $j=0;
            foreach($item as $data)
            {
                $product_id = $this->db->select("id")->from("whp_products")->where("cat_no",$data['cat_no'])->where("del","0")->get()->row_array();
                
                $image = $this->db->select("image")->from("whp_product_image")->where("id",$product_id['id'])->where("del","0")->get()->result_array();
                $item[$j]['image']=$image;
                $j++;
            }
            
            $order[$i]['order_item'] = $item;
            $i++;
        }
        
        echo json_encode(array('order_list' => $order));
    }

}