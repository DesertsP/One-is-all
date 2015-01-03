<?php
//定义主题文件夹
$theme_dir = get_template_directory_uri();

//无限加载文章
add_action('wp_ajax_nopriv_ajax_index_post', 'ajax_index_post');
add_action('wp_ajax_ajax_index_post', 'ajax_index_post');
function ajax_index_post(){
    $paged = $_POST["paged"];
    $total = $_POST["total"];
    $the_query = new WP_Query( array("posts_per_page"=>get_option('posts_per_page'),"post_status"=>"publish","post_type"=>"post","paged"=>$paged) );
    while ( $the_query->have_posts() ){
        $the_query->the_post();
        get_template_part( 'content', get_post_format() );//这里是内容输出，如果你的首页是直接用的代码输出，则直接写在这里，注意PHP的开始结束符

    }
    wp_reset_postdata();
    if ( $total > $paged )    echo '<a id="show-more" href="#" data-total="'.$total.'" data-paged = "'.($paged + 1).'" class="show-more m-feed--loader">show more</a>';
    die;
}
function ajax_show_more_button(){

    if( 2 > $GLOBALS["wp_query"]->max_num_pages){
        return;
    }

    echo '<a id="show-more" href="#" data-paged = "2" data-total="'.$GLOBALS["wp_query"]->max_num_pages.'" class="show-more m-feed--loader">show more</a>';

}

//时间显示xx前
function the_timeago(){
  $suffix=' ago';
  $endtime='3456000';
  $year = ' year';
  $years = ' years';
  $month = ' months';
  $day = ' days';
  $hour = ' hours';
  $minute = ' mins';
  $second = ' seconds';
  if ($_SERVER['REQUEST_TIME'])
      $now_time = $_SERVER['REQUEST_TIME'];
  else
      $now_time = time();
  $m = 60;  // 一分钟
  $h = 3600;  //一小时有3600秒
  $d = 86400;  // 一天有86400秒
  $mo = 2592000;  //一个月有2592000秒
  $y = 31536000;  //一年有31536000秒
  $endtime = (int)$endtime;  // 结束时间
  $post_time = get_post_time('U', true);
  $past_time = $now_time - $post_time;  // 文章发表至今经过多少秒
  if($past_time < $m){ //小于1分钟
      $past_date = $past_time . $second;
  }else if ($past_time < $h){ //小于1小时
      $past_date = $past_time / $m;
      $past_date = floor($past_date);
      $past_date .= $minute;
  }else if ($past_time < $d){ //小于1天
      $past_date = $past_time / $h;
      $past_date = floor($past_date);
      $past_date .= $hour;
  }else if ($past_time < $mo){
      $past_date = $past_time / $d;
      $past_date = floor($past_date);
      $past_date .= $day;
  }else if ($past_time < $y){
      $past_date = $past_time / $mo;
      $past_date = floor($past_date);
      $past_date .= $month;
  }else if ($past_time < $y*2){
      $past_date = $past_time / $y;
      $past_date = floor($past_date);
      $past_date .= $year;
  }else if ($past_time < $y*4){
      $past_date = $past_time / $y;
      $past_date = floor($past_date);
      $past_date .= $years;
  }else{
      echo 'Long long ago';
      return;
  }
  echo $past_date . $suffix;
}

//去除链接的版本号
if(!function_exists('cwp_remove_script_version')){
    function cwp_remove_script_version( $src ){  return remove_query_arg( 'ver', $src ); }
    add_filter( 'script_loader_src', 'cwp_remove_script_version' );
    add_filter( 'style_loader_src', 'cwp_remove_script_version' );
}

// 只搜索文章，排除页面
add_filter('pre_get_posts','search_filter');
function search_filter($query) {
if ($query->is_search) {$query->set('post_type', 'post');}
return $query;}

// 新窗口打开评论链接
function hu_popuplinks($text) {
	$text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank'>", $text);
	return $text;
}
add_filter('get_comment_author_link', 'hu_popuplinks', 6);

//移除评论信息中的网站地址
function remove_comment_fields($fields) {
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields','remove_comment_fields');

//去除评论中的链接
remove_filter('comment_text', 'make_clickable', 9);

add_filter('comment_text', 'auto_nofollow');

//给评论中的链接自动加上nofollow
function auto_nofollow($content) {
    //return stripslashes(wp_rel_nofollow($content));

    return preg_replace_callback('/<a>]+/', 'auto_nofollow_callback', $content);
}

function auto_nofollow_callback($matches) {
    $link = $matches[0];
    $site_link = get_bloginfo('url');

    if (strpos($link, 'rel') === false) {
        $link = preg_replace("%(href=S(?!$site_link))%i", 'rel="nofollow" $1', $link);
    } elseif (preg_match("%href=S(?!$site_link)%i", $link)) {
        $link = preg_replace('/rel=S(?!nofollow)S*/i', 'rel="nofollow"', $link);
    }
    return $link;
}

//comment_popup_links只统计评论数
if (function_exists('wp_list_comments')) {
	// comment count
	add_filter('get_comments_number', 'comment_count', 0);
	function comment_count( $commentcount ) {
		global $id;
		$_commnets = get_comments('post_id=' . $id);
		$comments_by_type = &separate_comments($_commnets);
		return count($comments_by_type['comment']);
	}
}

// 评论回复构架
function themecomment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment;
    global $commentcount;
    if(!$commentcount) {
       $page = ( !empty($in_comment_loop) ) ? get_query_var('cpage')-1 : get_page_of_comment( $comment->comment_ID, $args )-1;
       $cpp = get_option('comments_per_page');
       $commentcount = $cpp * $page;
    }
    /* 区分普通评论和Pingback */
    switch ($pingtype=$comment->comment_type) {
    case 'pingback' : /* 标识Pingback */
    case 'trackback' : /* 标识Trackback */

?>

<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
  <div id="comment-<?php comment_ID(); ?>">
    <div class="comment-author vcard pingback">
      <span class="fn pingback"><?php comment_date('Y-m-d') ?> &raquo; <?php comment_author_link(); ?></span>
    </div>
  </div>

  <?php
    break;
    /* 标识完毕 */
    default : /* 普通评论部分 */ 
    if(!$comment->comment_parent){ ?>

<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

<article id="comment-<?php comment_ID(); ?>" class="comment-body">


<div class="comment-body-header">

<header class="comment-header">
<span class="comment-author"><?php printf( __( '<span class="fn">%s</span>'), get_comment_author_link() ); ?>
<span class="reply"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => __('[ Reply ]')))) ?></span></span>
<span class="datetime"><?php comment_date('Y-m-d') ?> <?php comment_time() ?> </span>
</header>

<?php echo get_avatar( $comment, $size = '40'); ?>

<span class="floor"><?php printf('%1$s L', ++$commentcount); ?></span>

<div class="clear"></div>
</div>


<section class="comment-content">
<?php comment_text(); ?>
</section>  

</article>

<?php }else{?>

<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

<article id="comment-<?php comment_ID(); ?>" class="comment-body comment-children-body">

<div class="comment-body-header">

<header class="comment-header">
<span class="comment-author"><?php $parent_id = $comment->comment_parent; $comment_parent = get_comment($parent_id); printf(__('%s'), get_comment_author_link()) ?></span>
<span class="datetime"><?php comment_date('Y-m-d') ?> <?php comment_time() ?> </span>
</header>

<?php echo get_avatar( $comment, $size = '40'); ?>

<span class="floor"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => __('[ Reply ]')))) ?></span>

<div class="clear"></div>
</div>

<section class="comment-content">


<?php comment_text(); ?>
</section>  

</article>

<?php }
break; /* 普通评论标识完毕 */
  }
}

//Mini Gavatar Cache by Willin Kan. Modify by zwwooooo 
/*
function my_avatar_admin($avatar) {
     $tmp = strpos($avatar, 'http');
     $g = substr($avatar, $tmp, strpos($avatar, "'", $tmp) - $tmp);
     $tmp = strpos($g, 'avatar/') + 7;
     $f = substr($g, $tmp, strpos($g, "?", $tmp) - $tmp);
     $w = home_url(); // $w = get_bloginfo('url');
     $e = preg_replace('/wordpress\//', '', ABSPATH) .'avatar/'. $f .'.jpg';
     $t = 604800; //设定7天, 单位:秒
     if ( empty($default) ) $default = $w. '/avatar/default.jpg';
     if ( !is_file($e) || (time() - filemtime($e)) > $t ) //当头像不存在或者文件超过7天才更新
         copy(htmlspecialchars_decode($g), $e);
     else
         $avatar = strtr($avatar, array($g => $w.'/avatar/'.$f.'.jpg'));
     if (filesize($e) < 500) copy($default, $e);
     return $avatar;
 }
 add_filter('get_avatar', 'my_avatar_admin');
*/
 
//自动转义邮箱地址 By Ludou
function security_remove_emails($content) {
    $pattern = '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/i';
    $fix = preg_replace_callback($pattern, "security_remove_emails_logic", $content);

    return $fix;
}

function security_remove_emails_logic($result) {
    return antispambot($result[1]);
}

add_filter( 'the_content', 'security_remove_emails', 20 );
add_filter( 'comment_text', 'security_remove_emails', 20 );

//自定义表情路径
function custom_smilies_src($src, $img){
    return get_template_directory_uri() . '/images/smilies/' . $img;
}
add_filter('smilies_src', 'custom_smilies_src', 10, 2);

// 反全英文垃圾评论
	function scp_comment_post( $incoming_comment ) {
		$pattern = '/[一-龥]/u';

		if(!preg_match($pattern, $incoming_comment['comment_content'])) {
			ajax_comment_err( "You should type some Chinese word (like \"你好\") in your comment to pass the spam-check, thanks for your patience! 您的评论中必须包含汉字!" );
		}
		return( $incoming_comment );
	}
	add_filter('preprocess_comment', 'scp_comment_post');

	/**
	 * when comment check the comment_author comment_author_email
	 * @param unknown_type $comment_author
	 * @param unknown_type $comment_author_email
	 * @return unknown_type
	 *防止访客冒充博主发表评论 by Winy
	 */
	function CheckEmailAndName(){
		global $wpdb;
		$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
		$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
		if(!$comment_author || !$comment_author_email){
			return;
		}
		$result_set = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE display_name = '" . $comment_author . "' OR user_email = '" . $comment_author_email . "'");
		if ($result_set) {
			if ($result_set[0]->display_name == $comment_author){
				ajax_comment_err(__('You CANNOT use this name.'));//昵称
			}else{
				ajax_comment_err(__('You CANNOT use this email.'));//邮箱
			}
			fail($errorMessage);
		}
	}
	add_action('pre_comment_on_post', 'CheckEmailAndName');



// 去掉Category函数里的rel泛滥的HTML5错误
foreach(array(
    'rsd_link',//rel="EditURI"
    'index_rel_link',//rel="index"
    'start_post_rel_link',//rel="start"
    'wlwmanifest_link'//rel="wlwmanifest"
  ) as $xx)
  remove_action('wp_head',$xx);//X掉以上
  //rel="category"或rel="category tag", 这个最巨量
  function the_category_filter($thelist){
    return preg_replace('/rel=".*?"/','rel="tag"',$thelist);
  } 
  add_filter('the_category','the_category_filter');

//* Mini Pagenavi v1.0 by Willin Kan.
/*
function pagenavi(  $p = 2  ) {
if ( is_singular() ) return;
global $wp_query, $paged;
$max_page = $wp_query->max_num_pages;
if ( $max_page == 1 ) return;
if ( empty( $paged ) ) $paged = 1;
if ( $paged > 1 ) p_link( $paged - 1, '更新的文章', '<span class="newer-post">&laquo; Prev</span>' );
//echo '<span class="page-numbers pagenum-nav">' . $paged . ' / ' . $max_page . ' </span> ';
if ( $paged > $p + 1 ) p_link( 1, '第一页' );
if ( $paged > $p + 2 ) echo '<span class="page-numbers">...</span>';
for( $i = $paged - $p; $i <= $paged + $p; $i++ ) {
if ( $i > 0 && $i <= $max_page ) $i == $paged ? print "<span class='page-numbers current'>{$i}</span> " : p_link( $i );
}
if ( $paged < $max_page - $p - 1 ) echo '<span class="page-numbers">...</span>';
if ( $paged < $max_page - $p ) p_link( $max_page, '最后一页' );
if ( $paged < $max_page ) p_link( $paged + 1,'较旧的文章', '<span class="older-post">Next &raquo;</span>' );
}
function p_link( $i, $title = '', $linktype = '' ) {
if ( $title == '' ) $title = "第 {$i} 页";
if ( $linktype == '' ) { $linktext = $i; } else { $linktext = $linktype; }
echo "<a class='page-numbers' href='", esc_html( get_pagenum_link( $i ) ), "' title='{$title}'>{$linktext}</a> ";
}
*/

 /* Archives list by zwwooooo | http://zww.me */
 function zww_archives_list() {
     if( !$output = get_option('zww_archives_list') ){
         $the_query = new WP_Query( 'posts_per_page=-1&ignore_sticky_posts=1' ); //update: 加上忽略置顶文章
         $year=0; $mon=0; $i=0; $j=0;
         while ( $the_query->have_posts() ) : $the_query->the_post();
             $year_tmp = get_the_time('Y');
             $mon_tmp = get_the_time('m');
             $y=$year; $m=$mon;
             if ($mon != $mon_tmp && $mon > 0) $output .= '</ul>';
             if ($year != $year_tmp && $year > 0) $output .= '';
             if ($year != $year_tmp) {
                 $year = $year_tmp;
                 $output .= '<h2 class="al_year">'. $year .' 年</h2>'; //输出年份
             }
             if ($mon != $mon_tmp) {
                 $mon = $mon_tmp;
                 $output .= '<p style="font-size:20px"><span class="al_mon">'. $mon .' 月</span></p><ul class="al_post_list">'; //输出月份
             }
             $output .= '<li>'. get_the_time('d日: ') .'<a href="'. get_permalink() .'">'. get_the_title() .'</a> <em>('. get_comments_number('0', '1', '%') .')</em></li>'; //输出文章日期和标题
         endwhile;
         wp_reset_postdata();
         $output .= '</ul>';
         update_option('zww_archives_list', $output);
     }
     echo $output;
 }
 function clear_zal_cache() {
     update_option('zww_archives_list', ''); // 清空 zww_archives_list
 }
 add_action('save_post', 'clear_zal_cache'); // 新发表文章/修改文章时

 /* -----------------------------------------------
 <;<小牆>> Anti-Spam v1.9 by Willin Kan.
 */
 //建立
 class anti_spam {
   function anti_spam() {
     if ( !is_user_logged_in() ) {
       add_action('template_redirect', array($this, 'w_tb'), 1);
       add_action('pre_comment_on_post', array($this, 'gate'), 1);
       add_action('preprocess_comment', array($this, 'sink'), 1);
     }
   }
   //設欄位
   function w_tb() {
     if ( is_singular() ) {
       ob_start(create_function('$input', 'return preg_replace("#textarea(.*?)name=([\"\'])comment([\"\'])(.+)/textarea>#",
	   "textarea$1name=$2w$3$4/textarea><textarea name=\"comment\" cols=\"60\" rows=\"4\" style=\"display:none\"></textarea>", $input);') );
      }
   }
   //檢查
function gate() {
    if ( !empty($_POST['w']) && empty($_POST['comment']) ) {
      $_POST['comment'] = $_POST['w'];
    } else {
      $request = $_SERVER['REQUEST_URI'];
      $referer = isset($_SERVER['HTTP_REFERER'])         ? $_SERVER['HTTP_REFERER']         : '隐瞒';
      $IP      = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] . ' (透过代理)' : $_SERVER["REMOTE_ADDR"];
      $way     = isset($_POST['w'])                      ? '手动操作'                       : '未经评论表格';
      $spamcom = isset($_POST['comment'])                ? $_POST['comment']                : null;
      $_POST['spam_confirmed'] = "请求: ". $request. "\n来路: ". $referer. "\nIP: ". $IP. "\n方式: ". $way. "\n內容: ". $spamcom. "\n -- 记录成功 --";
    }
  }
   //處理
   function sink( $comment ) {
     if ( !empty($_POST['spam_confirmed']) ) {
       //方法一:直接擋掉, 將 die(); 前面兩斜線刪除即可.
       //die();
       //方法二:標記為spam, 留在資料庫檢查是否誤判.
       add_filter('pre_comment_approved', create_function('', 'return "spam";'));
       $comment['comment_content'] = "[ 小墙判断这是 Spam! ]\n". $_POST['spam_confirmed'];
     }
     return $comment;
   }
}
$anti_spam = new anti_spam();

// -- END ----------------------------------------

function ajax_comment_scripts() {
  global $pagenow;
    if(is_singular()){
    wp_enqueue_script( 'base', get_template_directory_uri() . '/comments-ajax.js', array(), '1.00', true);
    wp_localize_script('base', 'bigfa_Ajax_Url', array(
        "um_ajaxurl" => admin_url('admin-ajax.php')
    ));
  }
}
add_action('wp_enqueue_scripts', 'ajax_comment_scripts');
add_action('wp_ajax_nopriv_ajax_comment', 'ajax_comment');
add_action('wp_ajax_ajax_comment', 'ajax_comment');
function ajax_comment(){
    global $wpdb;
    $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
    $post = get_post($comment_post_ID);
    if ( empty($post->comment_status) ) {
        do_action('comment_id_not_found', $comment_post_ID);
        ajax_comment_err(__('Invalid comment status.'));
    }
    $status = get_post_status($post);
    $status_obj = get_post_status_object($status);
    if ( !comments_open($comment_post_ID) ) {
        do_action('comment_closed', $comment_post_ID);
        ajax_comment_err(__('Sorry, comments are closed for this item.'));
    } elseif ( 'trash' == $status ) {
        do_action('comment_on_trash', $comment_post_ID);
        ajax_comment_err(__('Invalid comment status.'));
    } elseif ( !$status_obj->public && !$status_obj->private ) {
        do_action('comment_on_draft', $comment_post_ID);
        ajax_comment_err(__('Invalid comment status.'));
    } elseif ( post_password_required($comment_post_ID) ) {
        do_action('comment_on_password_protected', $comment_post_ID);
        ajax_comment_err(__('Password Protected'));
    } else {
        do_action('pre_comment_on_post', $comment_post_ID);
    }
    $comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
    $comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
    $comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
    $comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;
    $user = wp_get_current_user();
    if ( $user->exists() ) {
        if ( empty( $user->display_name ) )
            $user->display_name=$user->user_login;
        $comment_author       = $wpdb->escape($user->display_name);
        $comment_author_email = $wpdb->escape($user->user_email);
        $comment_author_url   = $wpdb->escape($user->user_url);
        $user_ID        = $wpdb->escape($user->ID);
        if ( current_user_can('unfiltered_html') ) {
            if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
                kses_remove_filters();
                kses_init_filters();
            }
        }
    } else {
        if ( get_option('comment_registration') || 'private' == $status )
            ajax_comment_err(__('Sorry, you must be logged in to post a comment.'));
    }
    $comment_type = '';
    if ( get_option('require_name_email') && !$user->exists() ) {
        if ( 6 > strlen($comment_author_email) || '' == $comment_author )
            ajax_comment_err( __('Error: please fill the required fields (name, email).') );
        elseif ( !is_email($comment_author_email))
            ajax_comment_err( __('Error: please enter a valid email address.') );
    }
    if ( '' == $comment_content )
        ajax_comment_err( __('Error: please type a comment.') );
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
    if ( $comment_author_email ) $dupe .= "OR comment_author_email = '$comment_author_email' ";
    $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    if ( $wpdb->get_var($dupe) ) {
        ajax_comment_err(__('Duplicate comment detected; it looks as though you&#8217;ve already said that!'));
    }
    if ( $lasttime = $wpdb->get_var( $wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1", $comment_author) ) ) {
        $time_lastcomment = mysql2date('U', $lasttime, false);
        $time_newcomment  = mysql2date('U', current_time('mysql', 1), false);
        $flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
        if ( $flood_die ) {
            ajax_comment_err(__('You are posting comments too quickly.  Slow down.'));
        }
    }
    $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
    $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

    $comment_id = wp_new_comment( $commentdata );

    $comment = get_comment($comment_id);
    do_action('set_comment_cookies', $comment, $user);
    $comment_depth = 1;
    $tmp_c = $comment;
    while($tmp_c->comment_parent != 0){
        $comment_depth++;
        $tmp_c = get_comment($tmp_c->comment_parent);
    }
    $GLOBALS['comment'] = $comment; //your comments here  edit start
    if(!$comment->comment_parent){
    //以下是評論式樣, 不含 "回覆". 要用你模板的式樣 copy 覆蓋.
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

<article class="comment-body" id="comment-<?php comment_ID() ?>">


<div class="comment-body-header">

<header class="comment-header">
<span class="comment-author"><?php printf( __( '<cite class="fn">%s</cite>'), get_comment_author_link() ); ?></span>
<span class="datetime"><?php comment_date('Y-m-d') ?> <?php comment_time() ?> </span>
</header>

<?php echo get_avatar( $comment, $size = '44'); ?>

<div class="clear"></div>
</div>


<section class="comment-content">
<?php comment_text(); ?>
</section>

</article>

<?php }else{?>

<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

<article class="comment-body comment-children-body" id="comment-<?php comment_ID() ?>">

<div class="comment-body-header">

<header class="comment-header">
<span class="comment-author"><?php $parent_id = $comment->comment_parent; $comment_parent = get_comment($parent_id); printf(__('%s'), get_comment_author_link()) ?> to <a href="<?php echo "#comment-".$parent_id;?>" title="<?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $comment_parent->comment_content)), 0, 100,"..."); ?>"><?php echo $comment_parent->comment_author;?></a></span>
<span class="datetime"><?php comment_date('Y-m-d') ?> <?php comment_time() ?> </span>
</header>

<?php echo get_avatar( $comment, $size = '44'); ?>

<div class="clear"></div>
</div>

<section class="comment-content">


<?php comment_text(); ?>
</section>

</article>

    <?php } die();

}
function ajax_comment_err($a) {
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
}

//Comments Ajax end.

/* Ajax 评论分页 */
add_action('wp_ajax_nopriv_ajax_comment_page_nav', 'ajax_comment_page_nav');
add_action('wp_ajax_ajax_comment_page_nav', 'ajax_comment_page_nav');
function ajax_comment_page_nav(){
    global $post,$wp_query, $wp_rewrite;
    $postid = $_POST["um_post"];
    $pageid = $_POST["um_page"];
    $comments = get_comments('post_id='.$postid);
    $post = get_post($postid);
    if( 'desc' != get_option('comment_order') ){
        $comments = array_reverse($comments);
    }
    $wp_query->is_singular = true;
    $baseLink = '';
    if ($wp_rewrite->using_permalinks()) {
        $baseLink = '&base=' . user_trailingslashit(get_permalink($postid) . 'comment-page-%#%', 'commentpaged');
    }
    echo '<ol class="commentlist">';
    wp_list_comments('type=comment&callback=themecomment&max_depth=500&page=' . $pageid . '&per_page=' . get_option('comments_per_page'), $comments);//注意修改mycomment这个callback
    echo '</ol>';
    echo '<div id="commentnav" data-post-id="'.$postid.'">';
    paginate_comments_links('current=' . $pageid . '&prev_text=« Prev&next_text=Next »');
    echo '</div>';
    die;
}

// removes detailed login error information for security 移除wordpress登陆错误提示
add_filter('login_errors',create_function('$a', "return null;"));

if ( ! function_exists( 'twentythirteen_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_paging_nav() {
  global $wp_query;

  // Don't print empty markup if there's only one page.
  if ( $wp_query->max_num_pages < 2 )
    return;
  ?>
  <div id="container">
  <nav class="navigation paging-navigation" role="navigation">
    <div class="nav-links">

      <?php if ( get_next_posts_link() ) : ?>
      <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav"></span>Older', 'twentythirteen' ) ); ?></div>
      <?php endif; ?>

      <?php if ( get_previous_posts_link() ) : ?>
      <div class="nav-next"><?php previous_posts_link( __( 'Newer<span class="meta-nav"></span>', 'twentythirteen' ) ); ?></div>
      <?php endif; ?>

    </div><!-- .nav-links -->
  </nav><!-- .navigation -->
  </div>
  <?php
}
endif;