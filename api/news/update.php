<div class="row wrapper border-bottom white-bg page-heading">
	<h2>News</h2>
	<ol class="breadcrumb">
		<li>
			<a href="#">Home</a>
		</li>
		<li>
			<a href="#news">News</a>
		</li>
		<li class="active">
			<strong>New Article</strong>
		</li>
	</ol>
</div>
<div class="row wrapper-content  article" style="padding-left:0;padding-right:0;">
	<form id="articleForm">
		<input class="form-control" name="news_0_id" value="<?php echo $article['_id'];?>" style="display:none">
		<div class="ibox">
			<div class="ibox-content">
				<div class="row">
					<small class="pull-left">Created: <?php echo date('m/d/Y',strtotime($article['_timestampCreated']));?> by <strong><?php echo $createdBy;?></strong></small>
					<small class="pull-right">Last Updated: <?php echo date('m/d/Y',strtotime($article['_timestampModified']));?> by <strong><?php echo $modifiedBy;?></strong></small><br>
				</div>
				<div class="text-center">
					<span class="text-muted"><i class="fa fa-clock-o"></i> <?php echo date('m/d/Y',strtotime($article['_timestampCreated']));?></span>
					<br>
					<h1>
						<label><?php echo $article['title'];?></label>
						<input class="form-control" name="news_0_title" value="<?php echo $article['title'];?>">
					</h1>
				</div>
				<h3>Content</h3>
				<textarea name="news_0_htmlcontent" id="summernote"><?php echo $article['htmlcontent'];?></textarea>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<h5>Tags:</h5>
						<select name="news_0_tags" multiple class="tags" data-role="tagsinput">
							<?php
foreach($article['tags'] as $tag){
							?>
							<option value="<?php echo $tag['tagName'];?>"><?php echo $tag['tagName'];?></option>
							<?php
}
							?>
						</select>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-lg-12">
						<input id="articleSubmit" type="submit" class="btn btn-info">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script src="api/news/newsApi.js"></script>