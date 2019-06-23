<?php

namespace App\Controller;
define('ENCRYPT_METHOD', 'aes-256-cbc');

class Controller {
    protected $container;
    protected $db;

    public function __construct($container)
    {
        $this->container = $container;
        $this->db = new \PDO('mysql:host='.getenv("MYSQL_HOST").';dbname='.getenv("MYSQL_DATABASE"), getenv("MYSQL_USER"), getenv("MYSQL_PASSWORD"));
    }

    public function sendemail($values) {
        return true;
    }

    protected function encrypt($to_crypt) {
        $salt = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPT_METHOD));
        $to_crypt = openssl_encrypt($to_crypt, ENCRYPT_METHOD, $this->secret, 0, $salt);
        return $salt.$to_crypt;
    }
    protected function decrypt($to_decrypt) {
        $saltlen = openssl_cipher_iv_length(ENCRYPT_METHOD);
        return rtrim(openssl_decrypt(substr($to_decrypt, $saltlen), ENCRYPT_METHOD, $this->secret, 0, substr($to_decrypt, 0, $saltlen)), "\0");
    }

    protected function random($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
    }

    protected function getUInfo() {
        if(!isset($_SERVER["HTTP_X_AUTH_KEY"])){
            $datas = ["errors" => ["message" => "Unspecified API Token", "code" => 400 ]];
            return json_encode($datas);
        }
        $r = $this->db->prepare("SELECT `users`.`id`, `users`.`email`, `users`.`firstname`,`users`.`lastname`,`users`.`address1`,`users`.`address2`,`users`.`zip`,`users`.`city`,`users`.`country`,`users`.`level`, `plans`.`name` AS 'plan name', `plans`.`price` FROM `users` INNER JOIN `plans` ON `plans`.`id`=`users`.`subscription`  WHERE api_token = :api");
        $r->execute(["api" => $_SERVER["HTTP_X_AUTH_KEY"]]);
        return json_encode($r->fetch(\PDO::FETCH_ASSOC));
    }

    protected function _getUInfo($column) {
        if(!isset($_SERVER["HTTP_X_AUTH_KEY"])){
            $datas = ["errors" => ["message" => "Unspecified API Token", "code" => 400 ]];
            return json_encode($datas);
        }
        $r = $this->db->prepare("SELECT :col FROM users WHERE api_token = :api");
        $r->execute(["col" => $column,
            "api" => $_SERVER["HTTP_X_AUTH_KEY"]]
        );
        return $r->fetchColumn(0);
    }
}