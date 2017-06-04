<?php

namespace WPE\Author;


class Profile {

	private $user;

	public function init() {

		add_shortcode( 'author_profile', array( $this, 'get_author_profile' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );

	}

	public function load_assets() {

		wp_enqueue_style(
			'wpe-author-profile',
			WPEAP_URL . 'assets/css/main.css',
			array(),
			filemtime( WPEAP_PATH . 'assets/css/main.css' )
		);

	}

	public function get_author_profile() {

		global $wp_query;

		$output = '';

		if ( ! empty ( $wp_query->query_vars['author_name'] ) ) {

			$this->user = get_user_by( 'login', $wp_query->query_vars['author_name'] );

			$output .= $this->author_box();
			$output .= $this->author_posts();

		} else {

			$output .= __( 'Selecciona un usuario para visualizar su perfil', 'wpe-author-profile' );
		}

		return $output;

	}

	private function author_box() {

		$output = '';

		if ( empty( $this->user ) ) {
			return $output;
		}

		ob_start();

		set_query_var( 'user', $this->user );
		load_template( WPEAP_PATH . 'views/author-box.php' );

		$output = ob_get_clean();

		return $output;

	}

	private function author_posts() {

		$output = '';

		if ( empty( $this->user ) ) {
			return __('Este autor no existe.', 'wpe-author-profile' );
		}

		$author_posts = new \WP_Query( array(
			'nopaging' => 1,
			'author'   => $this->user->ID
		) );

		if ( ! $author_posts->have_posts() ) {
			return __('Este autor aún no tiene publicaciones.', 'wpe-author-profile' );
		}


		$output .= '<div class="author__posts">';
		$output .= '<h2 class="author__header">Publicaciones</h2>';


		while ( $author_posts->have_posts() ) {
			$author_posts->the_post();

			ob_start();

			load_template( WPEAP_PATH . '/views/author-post.php' );

			$output .= ob_get_clean();

		}

		$output .= '</div> <!-- .author__posts -->';

		wp_reset_postdata();

		return $output;


	}

}