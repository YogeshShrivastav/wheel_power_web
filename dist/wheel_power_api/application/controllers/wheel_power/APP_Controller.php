<?php
error_reporting(0);
class APP_Controller extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->leeway = 0;
        $this->timestamp = null;
        $this->supported_algs = array(
            'HS256' => array('hash_hmac', 'SHA256'),
            'HS512' => array('hash_hmac', 'SHA512'),
            'HS384' => array('hash_hmac', 'SHA384'),
            'RS256' => array('openssl', 'SHA256'),
            'RS384' => array('openssl', 'SHA384'),
            'RS512' => array('openssl', 'SHA512'),
        );
        $this->secret_key='insel_rect123';

        $this->url = $_SERVER['REQUEST_URI'];
        $this->str = substr(strrchr($this->url, '/'), 1);

        if($this->str != 'loginUser' && $this->str != 'login_customer')
        {
             $this->token_val = $this->getBearerToken();
             // $this->payload_val = $this->decode($this->token_val,$this->secret_key,['HS256']);
        }
        
        date_default_timezone_set('Asia/Calcutta');
        
        // $this->upload_dir = $_SERVER['DOCUMENT_ROOT']."/satmola/uploads/Products/";

        $this->upload_dir = $_SERVER['DOCUMENT_ROOT']."/today/uploads/"; 


    }

    public function getAuthorizationHeader(){
        $this->headers = null;
        if (isset($_SERVER['Authorization'])) {
            $this->headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { //cpanel
            $this->headers = trim($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $this->headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $this->requestHeaders = apache_request_headers();
            $this->requestHeaders = array_combine(array_map('ucwords', array_keys($this->requestHeaders)), array_values($this->requestHeaders));
            if (isset($this->requestHeaders['Authorization'])) {
                $this->headers = trim($this->requestHeaders['Authorization']);
            }
        }
        return $this->headers;
    }


    public function getBearerToken() {
    $this->headers = $this->getAuthorizationHeader();
    if (!empty($this->headers)) {
        if (preg_match('/Bearer\s(\S+)/', $this->headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
  }


    function decode($jwt, $key, array $allowed_algs = array())
    {

        $this->timestamp = is_null($this->timestamp) ? time() : $this->timestamp;
        if (empty($key)) {
            echo 'Key may not be empty';
        }

        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
          echo 'Wrong number of segments';
        }

        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = $this->jsonDecode($this->urlsafeB64Decode($headb64)))) {
            echo 'Invalid header encoding';
        }

        if (null === $payload = $this->jsonDecode($this->urlsafeB64Decode($bodyb64))) {
            echo 'Invalid claims encoding';
        }

        if (false === ($sig = $this->urlsafeB64Decode($cryptob64))) {
            echo 'Invalid signature encoding';
        }
        if (empty($header->alg)) {
            echo 'Empty algorithm';
        }

        if (empty($this->supported_algs[$header->alg])) {
            echo 'Algorithm not supported';
        }

        if (!in_array($header->alg, $allowed_algs)) {
            echo 'Algorithm not allowed';
        }

        if (is_array($key) || $key instanceof \ArrayAccess) {
            if (isset($header->kid)) {
                if (!isset($key[$header->kid])) {
                    echo '"kid" invalid, unable to lookup correct key';
                }
                $key = $key[$header->kid];
            } else {
                echo '"kid" empty, unable to lookup correct key';
            }
        }
        if (!$this->verify("$headb64.$bodyb64", $sig, $key, $header->alg)) {
            echo 'Signature verification failed';
        }
        if (isset($payload->nbf) && $payload->nbf > ($this->timestamp + $this->leeway)) {
            echo 'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->nbf);
        }
        if (isset($payload->iat) && $payload->iat > ($this->timestamp + $this->leeway)) {
            echo 'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->iat);
        }
        if (isset($payload->exp) && ($this->timestamp - $this->leeway) >= $payload->exp) {
            echo 'Expired token';
        }
        return $payload;
    }


  public function encode($payload, $key, $alg = 'HS256', $keyId = null, $head = null)
    {
        $header = array('typ' => 'JWT', 'alg' => $alg);
        if ($keyId !== null) {
            $header['kid'] = $keyId;
        }
        if ( isset($head) && is_array($head) ) {
            $header = array_merge($head, $header);
        }
        $segments = array();
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
        $signing_input = implode('.', $segments);
        $signature = $this->sign($signing_input, $key, $alg);
        // echo "LL";
        // echo $signature;
        // exit;
        $segments[] = $this->urlsafeB64Encode($signature);
        return implode('.', $segments);
    }

  public function sign($msg, $key, $alg = 'HS256')
    {
      global $supported_algs;

        if (empty($this->supported_algs[$alg])) {
            echo 'Algorithm not supported';
        }
        list($function, $algorithm) = $this->supported_algs[$alg];

        switch($function) {
            case 'hash_hmac':
                return hash_hmac($algorithm, $msg, $key, true);
            case 'openssl':
                $signature = '';
                $success = openssl_sign($msg, $signature, $key, $algorithm);
                if (!$success) {
                    // throw new DomainException("OpenSSL unable to sign data");
                } else {
                    return $signature;
                }
        }
    }

  public function verify($msg, $signature, $key, $alg)
    {
      global $supported_algs;
        if (empty($this->supported_algs[$alg])) {
            echo 'Algorithm not supported';
        }
        list($function, $algorithm) = $this->supported_algs[$alg];
        switch($function) {
            case 'openssl':
                $success = openssl_verify($msg, $signature, $key, $algorithm);
                if ($success === 1) {
                    return true;
                } elseif ($success === 0) {
                    return false;
                }
                // returns 1 on success, 0 on failure, -1 on error.
                echo 'OpenSSL error: ' . openssl_error_string();
            case 'hash_hmac':
            default:
                $hash = hash_hmac($algorithm, $msg, $key, true);
                if (function_exists('hash_equals')) {
                    return hash_equals($signature, $hash);
                }
                $len = min(safeStrlen($signature), safeStrlen($hash));
                $status = 0;
                for ($i = 0; $i < $len; $i++) {
                    $status |= (ord($signature[$i]) ^ ord($hash[$i]));
                }
                $status |= (safeStrlen($signature) ^ safeStrlen($hash));
                return ($status === 0);
        }
    }

  public function jsonDecode($input)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            $this->handleJsonError($errno);
        } elseif ($obj === null && $input !== 'null') {
            echo 'Null result with non-null input';
        }
        return $obj;
    }

  public function jsonEncode($input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
          $this->handleJsonError($errno);
        } elseif ($json === 'null' && $input !== null) {
            echo 'Null result with non-null input';
        }
        return $json;
    }

  public function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

  public function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters' //PHP >= 5.3.3
        );
        // throw new DomainException(
        //     isset($messages[$errno])
        //     ? $messages[$errno]
        //     : 'Unknown JSON error: ' . $errno
        // );
    }

    public function safeStrlen($str)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, '8bit');
        }
        return strlen($str);
    }



}
