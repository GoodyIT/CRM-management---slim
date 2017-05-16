<div class="row wrapper border-bottom white-bg page-heading">
	<h2>News</h2>
	<ol class="breadcrumb">
		<li>
			<a href="#">Home</a>
		</li>
		<li class="active">
			<a href="#news">News</a>
		</li>
		<li class="active">
			<strong>Article</strong>
		</li>
	</ol>
</div>
<div class="row animated fadeInRight article" style="padding-left:0;padding-right:0;">
	<div class="ibox">
		<div class="ibox-content">
			<div class="row">
				<small class="pull-left">Created: <?php echo date('m/d/Y',strtotime($article['_timestampCreated']));?> by <strong><?php echo $createdBy;?></strong></small>
				<small class="pull-right">Last Updated: <?php echo date('m/d/Y',strtotime($article['_timestampModified']));?> by <strong><?php echo $modifiedBy;?></strong></small><br>
			</div>
			<div class="text-center article-title">
				<span class="text-muted"><i class="fa fa-clock-o"></i> <?php echo date('m/d/Y',strtotime($article['_timestampCreated']));?></span>
				<h1>
					<?php echo $article['title'];?>
				</h1>
			</div>
			<div class="row wrapper-content wrapper">
				<?php echo $article['htmlcontent'];?>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-6">
					<h5>Tags:</h5>
					<?php
if((!empty($article['tags'])) && (is_array($article['tags'])) ){
	foreach($article['tags'] as $tag){
					?>
					<a href="#news/sort/<?php echo $tag['tagName'];?>"><button class="btn btn-white btn-xs" type="button"><?php echo $tag['tagName']; ?></button></a>
					<?php }}?>
				</div>
			</div>
		</div>
	</div>
</div>