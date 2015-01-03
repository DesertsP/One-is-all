<?php get_header(); ?>
<div id="main">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php get_template_part( 'content', get_post_format() ); ?>

<?php endwhile;?>
<?php endif; ?>

<?php comments_template( '', true ); ?>

</div>

<?php get_footer(); ?>