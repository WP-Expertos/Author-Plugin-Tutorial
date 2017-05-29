<?php

namespace WPE\Author;


class Profile {

	private static $user;

	/**
	 * Profile constructor.
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'load_assets' ) );

	}

	public function init() {

		add_shortcode( 'author_profile', array( &$this, 'author_profile_code' ) );

	}

	public function load_assets() {

		wp_enqueue_style(
			'wpe-author-profile',
			WPEAP_URL . 'assets/css/main.css',
			array(),
			filemtime( WPEAP_PATH . 'assets/css/main.css' )
		);

	}

	public function author_profile_code( $atts ) {

		return $this->print_author_profile();

	}

	public function print_author_profile() {

		global $wp_query;

		$output = '';

		if ( ! empty ( $wp_query->query_vars['author_name'] ) ) {

			self::$user = get_user_by( 'login', $wp_query->query_vars['author_name'] );

			$output .= $this->author_box();
			$output .= $this->author_posts();

		} else {

			$output .= wpautop( 'Selecciona un usuario para visualizar su perfil' );
		}

		return $output;

	}

	public function author_box() {

		$output = '';

		if ( empty( self::$user ) ) {
			return $output;
		}

		ob_start();

		set_query_var( 'user', self::$user );
		load_template( WPEAP_PATH . 'views/author-box.php' );

		$output = ob_get_contents();
		ob_clean();

		return $output;

	}

	public function author_posts() {

		$output = '';

		if ( empty( self::$user ) ) {
			return $output;
		}

		$author_posts = new \WP_Query( array(
			'nopaging' => 1,
			'author'   => self::$user->ID
		) );

		if ( $author_posts->have_posts() ) {

			$output .= '<div class="author__posts">';
			$output .= '<h2 class="author__header">Publicaciones</h2>';


			while ( $author_posts->have_posts() ) {
				$author_posts->the_post();

				ob_start();

				load_template( WPEAP_PATH . '/views/author-post.php' );

				$output .= ob_get_contents();
				ob_clean();

			}

			$output .= '</div> <!-- .author__posts -->';

			wp_reset_postdata();
		} else {

			$output .= wpautop( 'Este autor aún no tiene publicaciones.' );

		}

		return $output;


	}

}