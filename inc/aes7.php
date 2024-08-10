<?php
const sys_key = 'itluorgg87y65ki6';
class AES{
    public static function encrypt($string, $key){
		$string = serialize($string);
        // openssl_encrypt 加密不同Mcrypt，对秘钥长度要求，超出16加密结果不变
        $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return strtolower(bin2hex($data));
    }
    public static function decrypt($string, $key){
        $decrypted = openssl_decrypt(hex2bin($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return unserialize($decrypted);
    }
}
?>