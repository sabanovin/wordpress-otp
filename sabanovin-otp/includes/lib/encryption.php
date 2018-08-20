<?php


class AESEncryption_OTP {
	 public static function encrypt_data($string, $pass)
    {
        $result = '';
        for($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($pass, ($i % strlen($pass))-1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }
    public static function decrypt_data($string, $pass)
    {
        $result = '';
        $string = base64_decode($string);

        for($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($pass, ($i % strlen($pass))-1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}
?>