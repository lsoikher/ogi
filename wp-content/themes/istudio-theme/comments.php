<?php if(istOption('showside')){$showside='sidecomments';}else{$showside='postcomments';}if(istOption('pagenavnum')=='default'){$pnavcalss='navigation';}else{$pnavcalss='istudio_pagenavi';}$ctrlentry=istOption('ctrlentry');?>
<div class="<?php echo $showside;?>">
	<?php if(post_password_required()){?><p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.','iLost');?></p>
</div>
<?php return;}
if(have_comments()){?>
  <h3 id="comments"><?php _e('Responses to ','iStudio');?>&#8220;<?php the_title();?>&#8221;</h3>
    
	<?php /*if(get_option('page_comments')){$comment_pages=paginate_comments_links('echo=0');if($comment_pages){?><div class="navigation"><div class="<?php echo $pnavcalss;?>"><?php echo $comment_pages;?></div><div class="clear"></div></div><?php }}*/?>
  
	<ol class="commentlist"><?php wp_list_comments(array('callback'=>'istudio_custom_comments'));?></ol>
  
	<?php if(get_option('page_comments')){$comment_pages=paginate_comments_links('echo=0');if($comment_pages){?><div class="navigation"><div class="<?php echo $pnavcalss;?>"><?php echo $comment_pages;?></div><div class="clear"></div></div><?php }}
}else{
if(!comments_open()){?>
	<p class="nocomments"><?php _e('Comments are closed.','iLost');?></p>
<?php }}
/*

  <?php if(function_exists('wp_list_comments')){$trackbacks=$comments_by_type['pings'];}?>
  <?php if($comments||comments_open()){?>
	
  <div id="comments">
       
    <div id="commentlist">
      <ol id="thecomments" class="commentlist">
			<?php if($comments&&count($comments)-count($trackbacks)>0){
				wp_list_comments('type=comment&callback=istudio_custom_comments');
			}else{?>
      	<li class="messagebox"><?php _e('No comments yet.','iStudio');?></li>
			<?php }?>
      </ol>
      <?php if(get_option('page_comments')){
				$comment_pages=paginate_comments_links('echo=0');
				if($comment_pages){?>
      <div class="navigation">
      	<div class="<?php echo $pnavcalss;?>"><?php echo $comment_pages;?></div>
      	<div class="clear"></div>
      </div>
      <?php }}
			if(pings_open()){?>
      <ol id="thetrackbacks" class="pingbacklist">
        <?php if($trackbacks){$trackbackcount=0;
				foreach($trackbacks as $comment):?>
        <li <?php echo $oddcomment;?>id="comment-<?php comment_ID();?>">
          <div class="list">    
          	<table class="out" border="0" cellspacing="0" cellpadding="0">
          		<tr><td class="topleft"></td><td class="top"></td><td class="topright"></td></tr><tr><td class="left"></td><td class="conmts">
          			<cite><?php comment_author_link();?></cite> 
          			<small>(<?php echo $pingtype;?>,<?php if($comment->comment_approved=='0'){?><em>Your comment is awaiting moderation.</em><?php }comment_date();?></small>)
          			<?php comment_text();?>
          		</td><td class="right"></td>
          		</tr><tr><td class="bottomleft"></td><td class="bottom"></td><td class="bottomright"></td></tr>
          	</table>  
          </div>
        </li>
        <?php endforeach;?>
        <?php }else{?>
        <li class="messagebox"><?php _e('No trackbacks yet.','iStudio');?></li>
        <?php }?>
      </ol>
      <?php }?>
      <div class="clear"></div>
    </div>
  </div>
<?php }
*/
comment_form('comment_notes_after=');/*
if(($comments||comments_open())&&$ctrlentry){?>
<script type="text/javascript">istoJS.loadCommentShortcut();(function(jQuery){istojQ=jQuery.noConflict();istojQ(document).ready(function(){istojQ("#respond #commentform .form-submit #submit").after("<label class=\"cereply\"><?php echo _e('Use Ctrl+Enter to Reply Comments.','iStudio');?></label>");});})(jQuery);</script>
<?php }
*/?>
</div>