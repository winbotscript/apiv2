<?php

namespace App\Controller;

class ProductController extends Controller
{

    public function getInfo($barcode)
    {
        $api = new \OpenFoodFacts\Api('food','fr-fr');
        $prd = $api->getProduct($barcode);
        $p = ["name" => $prd->__get("product_name_fr"),
            "image" => $prd->__get("selected_images")["front"]["display"]["fr"]];
        return json_encode($p);
    }
}