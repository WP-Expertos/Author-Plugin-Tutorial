<article id="<?php the_ID() ?>" <?php post_class( 'wpe__author__entry'); ?>>
    <header class="entry-header">
        <div class="entry-meta"><span class="screen-reader-text">Posted on</span> <?php the_date(); ?></div>
        <h3 class="entry-title">
            <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
    </header>
    <div class="entry-content">
		<?php the_content(); ?>
    </div>
</article>