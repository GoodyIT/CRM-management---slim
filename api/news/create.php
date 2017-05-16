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
		<div class="ibox">
			<div class="ibox-content">
				<div class="text-center">
					<h1>
						<label>Title</label>
						<input class="form-control" name="news_0_title">
					</h1>
				</div>
				<h3>Content</h3>
				<textarea id="summernote" name="news_0_htmlcontent"></textarea>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<h5>Tags:</h5>
						<select multiple class="tags" data-role="tagsinput" name="news_0_tags"></select>
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