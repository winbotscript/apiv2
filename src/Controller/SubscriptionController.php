<?php

namespace App\Controller;

class SubscriptionController extends Controller {

    public function GetPlans() {
        $r = $this->db->prepare("SELECT * FROM plans");
        $r->execute();
        $r = $r->fetchAll(\PDO::FETCH_ASSOC);
        return json_encode($r);
    }
    public function Subscribe($id) {
        $plan = $this->_getUInfo("subscription");
        if($plan != '1') {
            $datas = ["errors" => [
                "message" => "You already have active subscription"
            ]];
            return json_encode($datas);
        }
        if(!isset($_POST["iban"])) {
            $datas = ["errors" => ["iban" => "IBAN not specified"]];
            return json_encode($datas);
        }
        $i = new \IBAN($_POST["iban"]);
        if(!$i->Verify()) {
            $datas = ["errors" => ["iban" => "IBAN not correct"]];
            return json_encode($datas);
        }
        $r = $this->db->prepare("UPDATE `users` SET `subscription` = :id, `IBAN` = :iban WHERE `users`.`api_token` = :token");
        $r->execute([
            "id" => $id,
            "iban" => $this->encrypt($_POST["iban"]),
            "token" => $_SERVER["HTTP_X_AUTH_KEY"]
        ]);
        $datas = ["success" => ["message" => "Subscription valid, an administrator will be alerted"]];
        return json_encode($datas);
    }

    public function Unsubscribe() {
        $plan = $this->_getUInfo("subscription");
        if($plan == '1') {
            $datas = ["errors" => [
                "message" => "You have no valid subscription"
            ]];
            return json_encode($datas);
        }
        $r = $this->db->prepare("UPDATE `users` SET `subscription` = '1', `IBAN` = '' WHERE `users`.`api_token` = :token");
        $r->execute(["token" => $_SERVER["HTTP_X_AUTH_KEY"]]);
        $datas = ["success" => ["message" => "Unsubscription OK, your IBAN was deleted."]];
        return json_encode($datas);
    }
}