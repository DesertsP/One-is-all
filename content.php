<article class="post">

<div id="container">
            <header class="entry-header">
                <h1 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
            <div class="date">
            <p><a href="<?php the_permalink() ?>"><?php the_time('Y/m/d'); ?></a></p>
            </div>
            </header>
            

            <div class="entry-content">
              <?php if(!is_single()) {
                    the_excerpt();
                              }
                    else {
                    the_content(__('more'));} ?>
            </div>

          <footer class="entry-footer" role="contentinfo">
            <div class="meta meta-footer">
              <span class="meta-elements timeago">
               <?php the_timeago(); ?>
              </span>
                 ·
                <span class="meta-elements notecount">
                  <a href="<?php the_permalink() ?>#comments"><?php comments_number('0 Note', '1 Note','% Notes'); ?></a>
                </span>
            </div>
            <?php if ( ! is_single() ) : ?>
                <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
            <?php endif;?>
            <?php if ( is_single() ) : ?>
                <div class="bottom-border"></div>
            <?php endif;?>
</div>
          </footer>
        </article>
