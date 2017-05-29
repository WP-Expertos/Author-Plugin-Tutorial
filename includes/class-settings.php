<?php

namespace WPE\Author;


class Settings {

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'wpe_register_setting' ) );
		add_action( 'init', array( $this, 'eval_flush_rewrite_rules' ) );
		add_action( 'update_option_author_profile_page_id', array(
			&$this,
			'insert_shortcode'
		), 10, 2 );

	}

	public function wpe_register_setting() {

		add_settings_section(
			'general_author_section',
			'Perfil de autor',
			array( &$this, 'general_author_section_description' ),
			'general'
		);

		register_setting( 'general', 'author_profile_page_id' );

		add_settings_field(
			'author_profile_page_id',
			'This is the setting title',
			array( &$this, 'author_profile_page_id_select' ),
			'general',
			'general_author_section',
			array(
				'label_for' => 'author_profile_page_id'
			)
		);

	}

	public function general_author_section_description() {
		echo '';
	}

	public function author_profile_page_id_select( $args ) {

		echo '<select name="' . $args['label_for'] . '" id="' . $args['label_for'] . '">';

		echo '<option value="0">-- Selecciona --</option>';

		if ( ! is_wp_error( $pages = get_pages( array( 'status' => array( 'pending', 'draft', 'future' ) ) ) ) ) {
			foreach ( $pages as $page ) {

				$selected = ( $page->ID == get_option( 'author_profile_page_id' ) ) ? ' selected ' : '';
				echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';

			}
		}

		echo '</select>';
	}

	public function insert_shortcode( $old_value, $value ) {

		if ( $value == $old_value ) {
			return;
		}

		$page = get_post( $value );

		if ( '' == trim( $page->post_content ) ) {

			wp_update_post( array(
				'ID'           => $value,
				'post_content' => '[author_profile]'
			) );

		}

		add_option( 'next_time_force_reflush', '1' );

	}

	public function eval_flush_rewrite_rules() {

		if ( '1' == get_option( 'next_time_force_reflush' ) ) {

			flush_rewrite_rules();

			delete_option( 'next_time_force_reflush' );

		}

	}

	public function get_author_profile_page_id() {

		return ( 0 <= ( $id = get_option( 'author_profile_page_id' ) ) ) ? $id : false;

	}
}