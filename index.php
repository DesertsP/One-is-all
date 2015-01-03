<?php get_header(); ?>
<main id="main" role="main">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php get_template_part( 'content', get_post_format() ); ?>

<?php endwhile;?>

<?php twentythirteen_paging_nav();?>

<?php endif; ?>

</main>

<?php get_footer(); ?>