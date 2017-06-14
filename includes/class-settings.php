<?php

namespace WPE\Author;


class Settings {

	/**
	 * Crear el método init permite ejecutar las acciones cuando sea necesario
	 * no es preciso hacerlo al instanciar la clase.
	 */

	public function init() {

		add_action( 'admin_init', array( $this, 'wpe_register_setting' ) );
		add_action( 'init', array( $this, 'eval_flush_rewrite_rules' ) );

		/**
		 * Esta acción se ejecuta cuando actualizamos
		 * el ID de página de author, en las preferencias.
		 */

		add_action( 'update_option_author_profile_page_id', array(
			$this,
			'insert_shortcode'
		), 10, 2 );

	}

	/**
	 * Este método se encarga de crear el selector de página de
	 * perfil de autor en las preferencias.
	 */

	public function wpe_register_setting() {

		// Creamos las sección, al final de Generales
		add_settings_section(
			'general_author_section',
			'Perfil de autor',
			array( $this, 'general_author_section_description' ), // al registrar, llama este método.
			'general'
		);

		// Registramos el valor, para que se actualice en base de datos.
		register_setting( 'general', 'author_profile_page_id' );

		// Registramos el campo, asociado a la sección antes registrada.
		add_settings_field(
			'author_profile_page_id',
			'This is the setting title',
			array( $this, 'author_profile_page_id_select' ),
			'general',
			'general_author_section',
			array(
				'label_for' => 'author_profile_page_id'
			)
		);

	}

	/**
	 * Devuelve la descripción de la sección registrada.
	 * En este caso, nada.
	 */
	public function general_author_section_description() {
		echo '';
	}

	/**
	 * Imprime un select con el listado de páginas disponibles.
	 *
	 * @param $args
	 */
	public function author_profile_page_id_select( $args ) {

		echo '<select name="' . $args['label_for'] . '" id="' . $args['label_for'] . '">';

		echo '<option value="0">-- Selecciona --</option>';

		$author_profile_page_id = get_option( 'author_profile_page_id' );

		$pages = get_pages( array( 'status' => array( 'pending', 'draft', 'future' ) ) );

		if ( ! is_wp_error( $pages ) ) {

			foreach ( $pages as $page ) {

				printf(
					'<option value="%s" %s >%s</option>',
					$page->ID,
					selected( $author_profile_page_id, $page->ID, false ),
					$page->post_title
				);

			}

		}

		echo '</select>';
	}

	/**
	 * Este método inserta automáticamente en la página seleccionada
	 * el shortcode que imprime el perfil de author siempre que esta
	 * no tenga contenido, de lo contrario, no la toca, por seguridad.
	 *
	 * @param $old_value
	 * @param $value
	 */
	public function insert_shortcode( $old_value, $value ) {

		if ( $value === $old_value ) {
			return;
		}

		$page = get_post( $value );

		if ( '' == trim( $page->post_content ) ) {

			wp_update_post( array(
				'ID'           => $value,
				'post_content' => '[author_profile]'
			) );

		}

		// La próxima vez que cargue, reestablece las reglas de URL.
		add_option( 'next_time_force_reflush', '1' );

	}

	/**
	 * Reestablece las reglas de URL, hazlo.
	 */

	public function eval_flush_rewrite_rules() {

		if ( '1' === get_option( 'next_time_force_reflush' ) ) {

			// Si al cargar existe la "orden" de reestablecer las reglas de URL, hazlo.
			flush_rewrite_rules();

			// Elimina esa "orden" para que no ocurra cada vez que cargue.
			delete_option( 'next_time_force_reflush' );

		}

	}

	/**
	 * Evalua que está registrado el ID de página de author y lo devuelve.
	 * En caso de que no, revuelve false.
	 *
	 * Este método es utilizando por otras clases del plugin
	 * mediante Dependency Injection (DI).
	 *
	 * @return bool|mixed
	 */

	public function get_author_profile_page_id() {

		$id = get_option( 'author_profile_page_id' );

		// Utilizamos un ternario para devolver ID o false, si no existe ID.
		return ( 0 <= $id ) ? $id : false;

	}
}