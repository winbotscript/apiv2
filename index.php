<?php
require "vendor/autoload.php";
require "src/Container.php";


$router = new App\Router\Router($_GET["url"], $container);
header("Content-type: Application/Json");

/*
 * Available routes method :
 * GET - POST
 * $router->METHOD(PATH :OPTIONS, CALLABLE, NAME)->with(PARAM, REGEX)
 */

//TODO: Documentation propre @param et @return

$router->get    ('/', "Home#welcome" ); //Say hello

$router->get    ('/user', "User#GetInfo"); //Return user informations [DONE]
$router->post   ('/user/register', "User#Register"); //Register an user return true or false with error array //TODO:sendemail
$router->post   ('/user/login', "User#Login"); //Login user return API Token [DONE]
$router->get    ('/user/validate/:code', "User#Validate"); // Validate user email [DONE]
$router->post   ('/user/reset', "User#Reset"); //Reset Password and API Token //TODO

$router->get    ('/subscription', "Subscription#GetPlans"); //Return all available plans [DONE]
$router->post   ('/subscription/subscribe/:id', "Subscription#Subscribe"); //Subscribe user with SEPA $_POST["IBAN"] [DONE]
$router->delete ('/subscription', "Subscription#Unsubscribe"); //Unsubscribe user [DONE]

$router->get    ('/product/info/:barcode', "Product#GetInfo"); //Get informations on a product [DONE]

$router->get    ('/stock', "Stock#GetStock"); //Return all Stock and informations //TODO
$router->put    ('/stock', "Stock#AddItem"); //Add Item and infos //TODO
$router->post   ('/stock/:id', "Stock#EditItem"); //Edit item's informations //TODO
$router->delete ('/stock/:id', "Stock#RemoveItem"); //Remove item from stock //TODO

$router->get    ('/collect', "Collect#GetCollect"); //Get collects point //TODO
$router->post   ('/collect', "Collect#AskCollect"); //Ask for a collect on a point //TODO
$router->delete ('/collect/:id', "Collect#CancelCollect"); //Cancel a collect on a point //TODO
$router->get    ('/collect/route', "Collect#GetMap"); //Get collects point map //TODO

$router->get    ('/distribution', "Distribution#GetPoints"); //Get distribution points //TODO
$router->post   ('/distribution', "Distribution#AskPoint"); //Ask for a distribution point //TODO
$router->delete ('/distribution/:id', "Distribution#CancelPoint"); //Cancel a distribution point //TODO
$router->get    ('/distribution/route', "Distribution#GetMap"); //Get map for distribution //TODO

$router->run();
