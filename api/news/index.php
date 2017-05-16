<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
require '../app.php';
$apiObj = new apiclass($settings);
$app->config(array(
	'templates.path' => './',
));
$app->map('/', function () use ($app, $settings, $apiObj)  {
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("news");
	$news=array();
	$newscursor=$apiObj->mongoFind();
	$newscursor->sort(array('date'=>-1));
	$result['total'] = $newscursor->count();
	if((!empty($settings['news']['page'])) && ($settings['news']['page'] > 1)){
		$newscursor->skip($settings['news']['news_per_page'] * $settings['news']['page']);
	}
	$newscursor->limit($settings['news']['news_per_page']);
	foreach($newscursor as $articles){
		$results=$articles;
		array_push($news, $results);
	}
	$app->render('news.php', array('news' => $news, "apiObj"=>$apiObj, 'result'=>$result, 'settings'=>$settings));
})->via('GET','POST');
$app->map('/sort/:sortId', function ($sortId) use ($app, $settings, $apiObj)  {
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("news");
	$news=array();
	$newscursor=$apiObj->mongoFind(array('tags.tagName'=>$sortId));
	$newscursor->sort(array('date'=>-1));
	$result['total'] = $newscursor->count();
	if((!empty($settings['news']['page'])) && ($settings['news']['page'] > 1)){
		$newscursor->skip($settings['news']['news_per_page'] * $settings['news']['page']);
	}
	$newscursor->limit($settings['news']['news_per_page']);
	foreach($newscursor as $articles){
		$results=$articles;
		array_push($news, $results);
	}
	$app->render('news.php', array('news' => $news, "apiObj"=>$apiObj, 'result'=>$result, 'settings'=>$settings, 'sortId'=>$sortId));
})->via('GET','POST');
$app->map('/new', function () use ($app, $settings, $apiObj)  {
	$app->render('create.php', array('news' => $news, "apiObj"=>$apiObj, 'result'=>$result, 'settings'=>$settings));
})->via('GET','POST');
$app->map('/view/:articleId', function ($articleId) use ($app, $settings, $apiObj)  {
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("news");
	$article = $apiObj->mongoFindOne(array('_id' => $articleId));
	$apiObj->mongoSetCollection("user");
	$user = $apiObj->mongoFindOne(array('_id'=>$article['_createdBy']));
	$editedBy = $apiObj->mongoFindOne(array('_id'=>$article['_modifiedBy']));
	$createdBy = ucwords(strtolower($user['firstname'])).' '.ucwords(strtolower($user['lastname']));
	$modifiedBy = ucwords(strtolower($editedBy['firstname'])).' '.ucwords(strtolower($editedBy['lastname']));
	$app->render('article.php', array('article' => $article, 'modifiedBy' => $modifiedBy, 'createdBy' => $createdBy));
})->via('GET','POST');
$app->map('/edit/:articleId', function ($articleId) use ($app, $settings, $apiObj)  {
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("news");
	$article = $apiObj->mongoFindOne(array('_id' => $articleId));
	$apiObj->mongoSetCollection("user");
	$user = $apiObj->mongoFindOne(array('_id'=>$article['_createdBy']));
	$editedBy = $apiObj->mongoFindOne(array('_id'=>$article['_modifiedBy']));
	$createdBy = ucwords(strtolower($user['firstname'])).' '.ucwords(strtolower($user['lastname']));
	$modifiedBy = ucwords(strtolower($editedBy['firstname'])).' '.ucwords(strtolower($editedBy['lastname']));
	$app->render('update.php', array('article' => $article, 'modifiedBy' => $modifiedBy, 'createdBy' => $createdBy));
})->via('GET','POST');
$app->run();