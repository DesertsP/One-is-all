<?php get_header(); ?>
<div id="main">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<article class="post">

          <div class="date">
            <p><a href="<?php the_permalink() ?>"><?php the_time('Y-m-d'); ?></a></p>
          </div>

            <header class="entry-header">
                <h1 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
            </header>

            <div class="entry-content">
              <?php the_content('');?>
            </div>


        </article>


<?php endwhile;?>
<?php endif; ?>

<?php comments_template( '', true ); ?>
</div>

<?php get_footer(); ?>