
<div id="container">
<?php
  if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('Hey guy WTF are you doing?');
?>

<section id="comments">

<?php
  if ( post_password_required() ) { ?>
    <p class="nocommentsyet"><?php _e('本文已被密码保护，请输入密码以查看评论。'); ?></p>
</section>
  <?php
    return;
  }
?>

<?php
       if (function_exists('wp_list_comments')) { $trackbacks = $comments_by_type['pings']; }
       else { $trackbacks = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' AND (comment_type = 'pingback' OR comment_type = 'trackback') ORDER BY comment_date", $post->ID)); }
?>

<?php if($comments) : //如果有评论 ?>

  <?php if ( comments_open() ) : /**/ ?>

    <h3 class="comment-counts"><a href="#respond_box" title="Add a comment."><?php comments_number('No comment yet.', '1 comment so far.', '' . (count($comments)-count($trackbacks)) . ' comments so far.' );?></a></h3>

  <?php else : /*else comments_open*/ ?>

    <h3 class="comment-counts"><a href="#respond_box" title="Add a comment."><?php comments_number('', '1 comment so far.', '' . (count($comments)-count($trackbacks)) . ' comments so far.' );?></a></h3>

    <?php endif; /*end comments_open*/ ?>
  
  
  <?php if ( have_comments() ) :/**/ ?>

    <div id="comments-body">
      <ol class="commentlist">
        <?php wp_list_comments('type=all&callback=themecomment&max_depth=500'); ?>
      </ol>
      
    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
      <div id="commentnav" data-post-id="<?php echo $post->ID?>"><?php paginate_comments_links('prev_text=« Prev&next_text=Next »');?></div>
    <?php endif; /*end get_comment_pages_count */?>

    </div>

  <?php endif; /*end have_comments*/?>
  
<?php endif; /*end if($comments)*/?>
    
<?php if(comments_open()) : ?>

  <div id="respond_box">
  <div id="respond">
    <h3>Add a comment</h3>

    <div class="cancel-comment-reply">
      <small><?php cancel_comment_reply_link('Canel reply') ; ?></small>
    </div>
    
    <?php if ( get_option('comment_registration') && !$user_ID ) : ?>
    
      <div class="comment-tip"><p>You must be <a href="<?php echo wp_login_url(); ?>?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to add a comment.</p></div>
      
    <?php else : ?>
    
      <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
      
        <?php if ( $user_ID ) : ?>
        
          <div class="comment-tip"><p>
           <?php {
              $user = wp_get_current_user();
              $link = 'Logged in as: '.$user->display_name.'  <a href="' . get_settings('siteurl') . '/wp-login.php?action=logout">Logout &raquo;</a>';
              echo apply_filters('loginout', $link);
            } ?>
          </p></div>
          
        <?php else : ?>
        
          <div id="comment-author-info">
            <p>
          <input type="text" name="author" id="author" class="commenttext" value="<?php echo $comment_author; ?>" size="22" tabindex="1" placeholder="Name (Required)" required />
        </p>

        <p>
          <input type="email" name="email" id="email" class="commenttext" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" placeholder="Mail (Required, will not publish)" required />
        </p>
        <p>
          <input type="text" name="url" id="url" class="commenttext" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" placeholder="Site" />
        </p>
          </div>

        <?php endif; /*end if ( $user_ID )*/?>

        <div class="wp-smiles-img"><?php get_template_part('smiley'); ?></div>

        <div class="comment-textarea"><textarea name="comment" id="comment" tabindex="4" cols="50" rows="8"></textarea></div>
        <div class="comment-submit"><input class="submit" name="submit" type="submit" id="submit" tabindex="5" value=" Submit " /></div>
        <div class="clear"></div>
        <script type="text/javascript"> //Ctrl+Enter
        //<![CDATA[
        jQuery(document).keypress(function(e){
          if(e.ctrlKey && e.which == 13 || e.which == 10) { 
            jQuery(".submit").click();
            document.body.focus();
          } else if (e.shiftKey && e.which==13 || e.which == 10) {
            jQuery(".submit").click();
          }          
        });
    // ]]>
        </script>
        <?php comment_id_fields(); ?>
        <?php do_action('comment_form', $post->ID); ?>
      </form>
    <?php endif; /*end get_option('comment_registration') && !$user_ID*/?>
  </div>
  </div>

<?php else : ?>

<p class="nocommentsyet">Comments are closed.</p>

<?php endif; /*end if(comments_open())*/?>
</section>
 </div>
