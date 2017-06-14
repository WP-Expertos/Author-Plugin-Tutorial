<?php

/**
 * Utilizamos Namespaces, entras otras cosas para evitar
 * duplicado de clases con otros plugin de WordPress.
 */

namespace WPE\Author;


class Rewrite_Rules {

	/**
	 * Esta variable será el contenedor de la clase Settings,
	 * que se utilizando Dependency Injection, DI.
	 *
	 * @var Settings
	 */

	private $settings;

	/**
	 * Rewrite_Rules constructor.
	 * Se ejecuta en cuanto se instancia la clase.
	 *
	 * @param Settings $settings
	 */

	public function __construct( Settings $settings ) {

		// settings es igual a la instancia de la clase Settings.
		$this->settings = $settings;

	}

	/**
	 * Crear el método init permite ejecutar las acciones cuando sea necesario
	 * no es preciso hacerlo al instanciar la clase.
	 */

	public function init() {

		/**
		 * Al inicializar WordPress, se ejecutan los métodos necesarios.
		 *
		 * url: https://developer.wordpress.org/reference/functions/add_action/
		 */

		add_action( 'init', array( $this, 'custom_author_url' ) );
		add_action( 'init', array( $this, 'custom_rewrite_rule' ), 10, 0 );

	}

	/**
	 * Crea la regla de url que registará y leerá el parámetro author_name cuando el formato
	 * sea "Pretty Permalinks".
	 *
	 * https://codex.wordpress.org/Using_Permalinks
	 */

	function custom_rewrite_rule() {

		// Utilizando Dependency Injection obtenemos la página de author creada las preferencias.
		$author_profile_page_id = $this->settings->get_author_profile_page_id();

		// Si no hay id de página es que no hay ninguna asignada y la función termina aquí.
		if ( false === $author_profile_page_id ) {
			return;
		}

		//Si hay, pedimos la página completa.
		$page = get_post( $author_profile_page_id );


		if ( null !== $page && '' != $page->post_name ) {

			// Si existe la página y tiene slug, podemos crear la regla de url.
			add_rewrite_rule( '^' . $page->post_name . '/([^/]*)/?', 'index.php?page_id=4&author_name=$matches[1]', 'top' );

		}

	}

	/**
	 * Sobreescribe la url por defecto de cada autor.
	 * De esta forma podemos utilizar el nuevo perfil de autor cómo estándar.
	 */

	public function custom_author_url() {

		global $wp_rewrite;

		// Utilizando Dependency Injection obtenemos la página de author creada las preferencias.
		$page_id = $this->settings->get_author_profile_page_id();

		// Dejamos solo la parte final de la url de author, que es la que necesitamos, sin / final.
		$author_slug = untrailingslashit( str_replace( get_home_url(), '', get_permalink( $page_id ) ) );

		// Aquí se hace la modificación de la url por defecto de cada autor.
		$wp_rewrite->author_base = $author_slug;

	}

}