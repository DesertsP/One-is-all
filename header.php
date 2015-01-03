<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
<?php if( is_single() || is_page() ) {
    if( function_exists('get_query_var') ) {
        $cpage = intval(get_query_var('cpage'));
        $commentPage = intval(get_query_var('comment-page'));
    }
    if( !empty($cpage) || !empty($commentPage) ) {
        echo '<meta name="robots" content="noindex, nofollow" />';
        echo "\n";
    }
}
?>
<title><?php global $page, $paged;wp_title( '&raquo;', true, 'right' );bloginfo( 'name' );$site_description = get_bloginfo( 'description', 'display' );if ( $site_description && ( is_home() || is_front_page() ) ) echo " &raquo; $site_description";if ( $paged >= 2 || $page >= 2 ) echo ' | ' . sprintf( __( '第 %s 页'), max( $paged, $page ) );?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/script.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>"/>

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

  <header id="header" role="banner">
    <div class="header-inside">
          <div class="logo">
            <h1 class="site-title">
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                <?php bloginfo( 'name' ); ?>
              </a>
            </h1>
            <h2 class="site-description">
              <?php bloginfo( 'description' ); ?>
            </h2>
          </div>

            <div class="mobile-nav-panel">
    <i class="icon-reorder icon-large"></i>
  </div>
    <nav class="nav" role="navigation">
      <ul class="nav-menu">
        <li>
          <a href="<?php bloginfo('url'); ?>">Home</a>
        </li>

          <li>
            <a href="<?php bloginfo('url'); ?>/about">About</a>
          </li>

        <li>
          <a href="<?php bloginfo('url'); ?>/archive">Archive</a>
        </li>

        <li>
          <a href="<?php bloginfo('url'); ?>/links">Links</a>
        </li>

      </ul>
    </nav>

    </div>
  </header>
