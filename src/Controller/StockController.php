<?php

namespace App\Controller;
define('IN_STOCK', 1);
define('RESERVED', 2);

class StockController extends Controller {

    public function GetStock() {
        $r = $this->db->prepare("SELECT * FROM stock");
        $r->execute();
        $r = $r->fetchAll(\PDO::FETCH_ASSOC);
        return json_encode($r);
    }

    public function addItem() { //TODO : Check if exists
        $r = $this->db->prepare("INSERT INTO `stock` (`barcode`, `status`, `corridor`, `bay`, `rack`) VALUES (:barcode, '1', :corridor, :bay, :rack)");
        $r->execute([
            "barcode" => $_POST["barcode"],
            "corridor" => $_POST["corridor"],
            "bay" =>   $_POST["bay"],
            "rack" => $_POST["rack"]
        ]);
        $data = ["success" => ["message" => "Item added to stock as IN_STOCK"]];
        return json_encode($data);
    }
}