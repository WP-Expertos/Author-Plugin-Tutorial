<div class="author__wrapper">
    <div class="author__avatar">
		<?php echo get_avatar( $user->ID ); ?>
    </div>
    <h1 class="author__name"><?php echo esc_html( $user->display_name ); ?></h1>
    <div class="author__description">
		<?php echo apply_filters( 'the_content', $user->description ); ?>
    </div>
</div>