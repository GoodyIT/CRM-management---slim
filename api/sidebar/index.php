<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
require '../app.php';
use Mailgun\Mailgun;
use Firebase\Token\TokenException;
use Firebase\Token\TokenGenerator;
$app->config(array(
	'templates.path' => './',
));
$app->map('/', function () use ($app, $settings)  {
	$app->render('sidepanel.php', array('name' => $name,'roomId'=>$roomId,'roomLabel'=>$roomLabel,'usergroups'=>$usergroups,'manager'=>$manager,'token'=>$token));
})->via('GET','POST');
$app->run();