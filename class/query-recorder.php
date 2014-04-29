<?php
class Query_Recorder {

	function __construct( $plugin_file_path ) {
		$this->plugin_version = $GLOBALS['query_recorder_version'];
		$this->plugin_file_path = $plugin_file_path;
		$this->plugin_dir_path = plugin_dir_path( $plugin_file_path );
		$this->plugin_folder_name = basename( $this->plugin_dir_path );
		$this->plugin_basename = plugin_basename( $plugin_file_path );
		$this->plugin_base ='options-general.php?page=query_recorder';

		$this->required_cap = 'manage_options';

		if ( is_admin() ) {
			$this->admin_init();
		}

		add_filter( 'query', array( $this, 'record_query' ), 9999 ); // Set priority high to make sure it's the last filter to run
	}

	function admin_init() {
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'plugin_action_links' ) );
	}

	function plugin_action_links( $links ) {
		if ( !current_user_can( $this->required_cap ) ) return $links; // don't show the Settings link unless the user can access the Settings page
		$link = sprintf( '<a href="%s">%s</a>', admin_url( $this->plugin_base ), __( 'Settings', 'query-recorder' ) );
		array_unshift( $links, $link );
		return $links;
	}

	function record_query( $sql ) {
		if ( !preg_match( '@^(INSERT|UPDATE|DELETE|DROP|CREATE)@i', $sql ) ) {
			return $sql;
		}

		$ignore_strings = array( 
			"_transient",
			"`option_name` = 'cron'",
			"post_type=deprecated_log",
			"SET `comment_count`",
			"`meta_key` = '_edit_lock'",
			"_yoast_wpseo_linkdex"
		);
		foreach ( $ignore_strings as $string ) {
			if ( false !== strpos( $sql, $string ) ) {
				return $sql;
			}
		}

		$upload_dir = wp_upload_dir();
		file_put_contents( $upload_dir['basedir'] . '/recorded-queries.txt', $sql . "\n", FILE_APPEND );

		return $sql;
	}

}
