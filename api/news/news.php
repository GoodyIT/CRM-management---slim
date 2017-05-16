<div class="row wrapper border-bottom white-bg page-heading animated fadeInRight">
	<div class="col-sm-8">
		<h2>News</h2>
		<ol class="breadcrumb">
			<li>
				<a href="#">Home</a>
			</li>
			<?php
			if(!empty($sortId)){
			?>
			<li class="active">
				<a href="#news">News</a>
			</li>
			<li class="active">
				<strong><?php echo $sortId;?></strong>
			</li>
			<?php
			}else{
			?>
			<li class="active">
				<strong>News</strong>
			</li>
			<?php
			}
			?>
		</ol>
	</div>
		<div class="title-action">
			<?php
			if((!empty($_SESSION['api']['user']['permissionLevel'])) && (($_SESSION['api']['user']['permissionLevel'] != "user")||($_SESSION['api']['user']['permissionLevel'] != "USER"))){
			?>
			<a href="#news/create" class="btn btn-primary">New Article <i class="fa fa-plus"></i></a>
			<?php
			}
			?>
		</div>
</div>
<div class="animated fadeInRight blog">
	<div class="row wrapper" style="padding-left:0;padding-right:0;">
		<?php
		foreach($news as $newsArticle){
		?>
		<div class="ibox">
			<div class="ibox-content">
				<a href="#news/view/<?php echo $newsArticle['_id'];?>" class="btn-link article">
					<h2>
						<?php echo $newsArticle['title'];?> 
				</a>
				<?php
			if((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] != "user")){
				?>
					<a href="#news/edit/<?php echo $newsArticle['_id'];?>" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a> 
					<a deleteId="<?php echo $newsArticle['_id'];?>" class="btn btn-warning btn-xs articleDelete"><i class="fa fa-times"></i></a>
				<?php
			}
				?>
					</h2>
				<div class="small m-b-xs">
					<span class="text-muted"><i class="fa fa-clock-o"></i> <?php echo date('m/d/Y',strtotime($newsArticle['_timestampCreated']));?></span>
				</div>
				<p>
					<?php echo substr(strip_tags($newsArticle['htmlcontent']), 0 ,300); ?>. . .
				</p>
				<div class="row">
					<div class="col-md-6">
						<h5>Tags:</h5>
						<?php
			if((!empty($newsArticle['tags'])) && (is_array($newsArticle['tags'])) ){
				foreach($newsArticle['tags'] as $tag){
						?>
						<a href="#news/sort/<?php echo $tag['tagName'];?>">
							<button class="btn btn-white btn-xs" type="button">
								<?php echo $tag['tagName'];?>
							</button>
						</a>
						<?php
				}
			}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<div class="row">
			<div class="col-sm-6">
				<div class="pagination">Showing
					<?php echo ($settings['news']['news_per_page'] * $settings['news']['page']) - $settings['news']['news_per_page'] + 1;?> to
					<?php echo ($settings['news']['news_per_page'] * $settings['news']['page']);?> of
					<?php echo $result['total'];?> Articles
				</div>
			</div>
			<div class="col-sm-6">
				<ul class="pagination pull-right">
					<?php
					if($settings['page'] == 1){
						if(!empty($sortId)){
							echo ' <li class="paginate_button previous disabled" tabindex="0" ><a href="#news/sort/'.$sortId.'/page/1">Previous</a></li> ';
						}else{
							echo ' <li class="paginate_button previous disabled" tabindex="0" ><a href="#news/page/1">Previous</a></li> ';
						}
					} else {
						if(!empty($sortId)){
							echo ' <li class="paginate_button previous " tabindex="0" ><a href="#news/sort/'.$sortId.'/page/'.($settings['news']['page'] - 1).'">Previous</a></li> '; 
						}else{
							echo ' <li class="paginate_button previous " tabindex="0" ><a href="#news/page/'.($settings['news']['page'] - 1).'">Previous</a></li> '; 
						}
					}
					?>
					<?php
					$i = 1;
					if((empty($settings['news']['news_per_page'])) || ($settings['news']['news_per_page'] < 1)){
						$settings['news']['per_page'] = 100;   
					}
					for ($x = 1; $x <= $result['total']; $x++) {
						$active = "";
						if($i == $settings['news']['page']){
							$active = "active";
						}
						if(!empty($sortId)){
							echo  '<li class="paginate_button '.$active.'" tabindex="0"><a href="#news/sort/'.$sortId.'/page/'.$i.'">'.$i.'</a></li>';
						}else{
							echo  '<li class="paginate_button '.$active.'" tabindex="0"><a href="#news/page/'.$i.'">'.$i.'</a></li>';
						}
						$i++;
						$x = $i * $settings['news']['news_per_page'];
					} 
					?>
					<?php
					$i--;
					if($settings['news']['page'] < $i){
						if(!empty($sortId)){
							echo '  <li class="paginate_button next" tabindex="0" ><a href="#news/sort/'.$sortId.'/page/'. ($settings['news']['page'] + 1) . '">Next</a></li> ';
						}else{
							echo '  <li class="paginate_button next" tabindex="0" ><a href="#news/page/'. ($settings['news']['page'] + 1) . '">Next</a></li> ';
						}
					} else {
						if(!empty($sortId)){
							echo '  <li class="paginate_button next disabled" tabindex="0" ><a href="#news/sort/'.$sortId.'/page/'. ($settings['news']['page']) . '">Next</a></li> ';
						}else{
							echo '  <li class="paginate_button next disabled" tabindex="0" ><a href="#news/page/'. ($settings['news']['page']) . '">Next</a></li> ';
						}
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
<script src="api/news/newsApi.js"></script>