<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require './api/app.php';
$app->config(array(
    'templates.path' => './views',
));
$app->get('/', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    if($apiObj->userLoggedIn()){
       $app->render('main.php', array("apiObj"=>$apiObj, "settings"=> $settings));
    } else {
      $app->response->redirect($settings['base_uri'].''.$settings['login_page']);
    }
});

$app->run();