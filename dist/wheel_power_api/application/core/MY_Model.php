<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        header('Access-Control-allow-origin:*');
        $this->load->database();
        date_default_timezone_set('Asia/Calcutta'); 
    }

    function my_simple_crypt( $string, $action = 'e' ) 
    {
        // you may change these values to your own
        $secret_key = 'my_simple_secret_key';
        $secret_iv = 'my_simple_secret_iv';
    
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    
        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
    
        return $output;
    }
    
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

}

?>