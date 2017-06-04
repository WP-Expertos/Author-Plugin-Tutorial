<?php

namespace WPE\Author;


class Rewrite_Rules {

	private $settings;

	public function __construct( Settings $settings ) {

		$this->settings = $settings;

	}

	public function init() {

		add_action( 'init', array( $this, 'custom_author_url' ) );
		add_action( 'init', array( $this, 'custom_rewrite_rule' ), 10, 0 );

	}

	public function add_rewrite_tags() {
		add_rewrite_tag( '%author_name%', '([^&]+)' );
	}

	function custom_rewrite_rule() {

		$author_profile_page_id = $this->settings->get_author_profile_page_id();

		if ( false === $author_profile_page_id ) {
			return;
		}

		$page = get_post( $author_profile_page_id );

		if ( null !== $page && '' != $page->post_name ) {

			add_rewrite_rule( '^' . $page->post_name . '/([^/]*)/?', 'index.php?page_id=4&author_name=$matches[1]', 'top' );

		}

	}

	public function custom_author_url() {

		global $wp_rewrite;

		$page_id = $this->settings->get_author_profile_page_id();

		$author_slug = untrailingslashit( str_replace( get_home_url(), '', get_permalink( $page_id ) ) );

		$wp_rewrite->author_base = $author_slug;

	}

}