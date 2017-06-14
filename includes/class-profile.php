<?php

namespace WPE\Author;


class Profile {

	/**
	 * Usuario que será utilizando por los métodos de esta clase.
	 */

	private $user;

	/**
	 * Crear el método init permite ejecutar las acciones cuando sea necesario
	 * no es preciso hacerlo al instanciar la clase.
	 */

	public function init() {

		/**
		 * Registramos el shortcode que utilizaremos en nuestra página plantilla
		 * para cargar el contenido del perfil de author.
		 */

		add_shortcode( 'author_profile', array( $this, 'get_author_profile' ) );

		/**
		 * Registramos la carga del CSS.
		 */

		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );

	}

	/**
	 * Cargamos el archivo CSS, finalmente.
	 */

	public function load_assets() {

		wp_enqueue_style(
			'wpe-author-profile',
			WPEAP_URL . 'assets/css/main.css',
			array(),
			filemtime( WPEAP_PATH . 'assets/css/main.css' )
		);

	}

	/**
	 * Este método unifica la impresión en página del perfil del autor,
	 * con su bio y sus publicaciones.
	 *
	 * @return string
	 */

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

	/**
	 * Este método devuelve la información del author
	 * y es utlilizado por get_author_profile()
	 *
	 * @return string
	 */

	private function author_box() {

		$output = '';

		// Si no hay usuario, termina la función.
		if ( empty( $this->user ) ) {
			return $output;
		}

		// A partir de este punto, cualquier impresión se almacena en un buffer.
		ob_start();

		// Hey WordPress, carga el usuario que viene en $this->user ;)
		set_query_var( 'user', $this->user );

		// Cargamos la plantilla que da formato a la información del usuario.
		load_template( WPEAP_PATH . 'views/author-box.php' );

		// Paso el buffer a una variable y lo limpio.
		$output = ob_get_clean();

		return $output;

	}

	/**
	 * Este método devuelve las publicaciones del author
	 * y es utlilizado por get_author_profile()
	 *
	 * @return string
	 */

	private function author_posts() {

		$output = '';

		// Si el usuario no existe, termina el método.
		if ( empty( $this->user ) ) {
			return __( 'Este autor no existe.', 'wpe-author-profile' );
		}

		// Si llega hasta aquí, existe, carguemos sus publiaciones.
		$author_posts = new \WP_Query( array(
			'nopaging' => 1,
			'author'   => $this->user->ID
		) );

		// Existe pero no tiene publicaciones, termina el método.
		if ( ! $author_posts->have_posts() ) {
			return __( 'Este autor aún no tiene publicaciones.', 'wpe-author-profile' );
		}

		// Si llega hasta aquí, tiene plublicaciones, imprimimos.
		$output .= '<div class="author__posts">';
		$output .= '<h2 class="author__header">Publicaciones</h2>';

		while ( $author_posts->have_posts() ) {
			$author_posts->the_post();

			// A partir de este punto, cualquier impresión se almacena en un buffer.
			ob_start();

			// Cargamos la plantilla que da formato a cada publicación existente.
			load_template( WPEAP_PATH . '/views/author-post.php' );

			// Paso el buffer a una variable y lo limpio.
			$output .= ob_get_clean();

		}

		// Terminamos impresión.
		$output .= '</div> <!-- .author__posts -->';

		// Restauramos $post y por tanto el Loop principal de WordPress
		wp_reset_postdata();

		return $output;


	}

}