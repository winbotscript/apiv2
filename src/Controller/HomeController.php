<?php

namespace App\Controller;

class HomeController extends Controller {

    public function welcome($name = null) {
        $response = [
            "code" => 200,
            "message" => "Welcome to LeRelais API"
        ];
        if($this->container["debug"]) {
            $response = [
                "code" => 200,
                "message" => "DEBUG MODE ENABLED!"
            ];
        }
        return json_encode($response);
    }
}