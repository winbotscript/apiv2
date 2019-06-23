<?php

namespace App\Controller;

class UserController extends Controller {
    public function GetInfo() { //Header X-API-key
        return $this->getUInfo();
    }

    public function Register() {
        $r = $this->db->prepare("SELECT count(*) FROM users WHERE email = :e");
        $r->execute(["e" => $_POST["email"]]);
        $r = $r->fetchColumn(0);
        if($r != '0')
            $datas = ["errors" => ["message" => "An user already exists with this email"]];
        if(empty($_POST["email"]))
            $datas["email"] = "No email specified";
        if(empty($_POST["password"]))
            $datas["password"] = "No password specified";
        if(empty($_POST["firstname"]))
            $datas["firstname"] = "No firstname specified";
        if(empty($_POST["lastname"]))
            $datas["lastname"] = "No lastname specified";
        if(empty($_POST["address1"]))
            $datas["address1"] = "No address specified";
        if(empty($_POST["zip"]))
            $datas["zip"] = "No zip code specified";
        if(empty($_POST["city"]))
            $datas["city"] = "No zip code specified";
        if(empty($_POST["country"]))
            $datas["country"] = "No country code specified";
        if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
            $datas["email_valid"] = "Email address not valid";
        if($this->PasswordStrenth($_POST["password"]))
            $datas["password_complex"] = "Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";
        if(!empty($datas))
            return json_encode($datas);

        $r = $this->db->prepare("INSERT INTO `users` (`email`, `password`, `firstname`, `lastname`, `address1`, `address2`, `zip`, `city`, `country`, `token`, `api_token`, `reg_date`, `log_date`) VALUES (:email, :password, :firstname, :lastname, :address1, :address2, :zip, :city, :country, :token, :api_token, NOW(), NOW())");
        try  {
            $r->execute([
                "email"     => $_POST["email"],
                "password"  => $this->encrypt($_POST["password"]),
                "firstname" => $_POST["firstname"],
                "lastname"  => $_POST["lastname"],
                "address1"  => $_POST["address1"],
                "address2"  => isset($_POST['address2']) ? $_POST["address2"] : '',
                "zip"       => $_POST["zip"],
                "city"      => $_POST["city"],
                "country"   => $_POST["country"],
                "token"     => $this->random(64),
                "api_token" => $this->random(64)
            ]);
            $datas = ["success" => ["message" => "User registered"]];
            return json_encode($datas);
            } catch (\PDOException $e) {
                $datas = ["errors" => ["message" => "An error occurred, please contact an administrator", "details" => $e->getMessage(), "code" => $e->getCode()]];
                return json_encode($datas);
            }
    }

    public function Login() { //$_POST["email"] $_POST["password"]
        if(empty($_POST["email"]))
            $datas["email"] = "No email specified";
        if(empty($_POST["password"]))
            $datas["password"] = "No password specified";
        if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
            $datas["email_valid"] = "Email address not valid";
        if(!empty($datas))
            return json_encode($datas);
        $r = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $r->execute(["email" => $_POST["email"]]);
        $r = $r->fetch(\PDO::FETCH_ASSOC);
        $password = $this->decrypt($r["password"]);
        if($password == $_POST["password"]) {
            $datas = ["success" => ["message" => "You are now connected", "detail" => ["Token" => $r["api_token"]]]];
            return json_encode($datas);
        } else {
            $datas = ["errors" => ["message" => "Password incorrect"]];
            return json_encode($datas);
        }
    }

    public function Validate($token) {
        $r = $this->db->prepare("UPDATE `users` SET `valid` = '1', `token` = '' WHERE `users`.`token` = :token");
        $r->execute(["token" => $token]);
        if($r->rowCount() === 0) {
            $datas["errors"] = ["message" => "Unvalid token, please contact an administrator"];
            return json_encode($datas);
        } else {
            $datas["success"] = ["message" => "Email valid"];
            return json_encode($datas);
        }
    }

    public function Reset() { //$_POST["email"]

    }

    private function PasswordStrenth($password) {
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return true;
        }else{
            return false;
        }
    }

}