<?php
class Query_Recorder {

	function __construct( $plugin_file_path ) {
		$this->set_default_options();
		$this->load_options();

		$this->plugin_version = $GLOBALS['query_recorder_version'];
		$this->plugin_file_path = $plugin_file_path;
		$this->plugin_dir_path = plugin_dir_path( $plugin_file_path );
		$this->plugin_folder_name = basename( $this->plugin_dir_path );
		$this->plugin_basename = plugin_basename( $plugin_file_path );
		$this->plugin_base ='options-general.php?page=query-recorder';

		$this->required_cap = 'manage_options';

		if ( is_admin() ) {
			$this->admin_init();
		}

		add_filter( 'query', array( $this, 'record_query' ), 9999 ); // Set priority high to make sure it's the last filter to run
	}

	function admin_init() {
		add_action( 'admin_menu', array( $this, 'add_pages' ) );
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'plugin_action_links' ) );
	}

	function add_pages() {
		$options_page = add_options_page( __( "Query Recorder Options", 'query-recorder' ), __( "Query Recorder", 'query-recorder' ), $this->required_cap, 'query-recorder', array( $this, 'page_options' ) );
	}

	function plugin_action_links( $links ) {
		if ( !current_user_can( $this->required_cap ) ) return $links; // don't show the Settings link unless the user can access the Settings page
		$link = sprintf( '<a href="%s">%s</a>', admin_url( $this->plugin_base ), __( 'Settings', 'query-recorder' ) );
		array_unshift( $links, $link );
		return $links;
	}

	function record_query( $sql ) {
		if ( !empty( $this->options['record_queries_beggining_with'] ) ) {
			$record_queries_beggining_with = implode( '|', $this->options['record_queries_beggining_with'] );
			if ( !preg_match( '@^(' . $record_queries_beggining_with . ')@i', $sql ) ) {
				return $sql;
			}
		}

		foreach ( $this->options['exclude_queries'] as $string ) {
			if ( false !== strpos( $sql, $string ) ) {
				return $sql;
			}
		}

		$upload_dir = wp_upload_dir();
		file_put_contents( $this->options['saved_queries_file_path'], $sql . "\n", FILE_APPEND );

		return $sql;
	}

	function set_default_options() {
		// default option for "Save queries to file"
		$upload_dir = wp_upload_dir();
		$salt = strtolower( wp_generate_password( 5, false, false ) );
		$saved_queries_file_path = sprintf( '%srecorded-queries-%s.sql', trailingslashit( $upload_dir['basedir'] ), $salt );
		$this->default_options['saved_queries_file_path'] = $saved_queries_file_path;
	
		// default option for "Exclude queries containing"
		$this->default_options['exclude_queries'] = array( '_transient', '`option_name` = \'cron\'' );

		// default option for "Record queries that begin with"
		$this->default_options['record_queries_beggining_with'] = array( 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE' );
	}

	function load_options() {
		$update_options = false;

		$this->options = get_option( 'query_recorder' );

		// if no options exist then this is a fresh install, set up some default options
		if ( empty( $this->options ) ) {
			$this->options = $this->default_options;
			$update_options = true;
		} else {
			$this->options = wp_parse_args( $this->options, $this->default_options );
		}

		if ( $update_options ) {
			update_option( 'query_recorder', $this->options );
		}

		// allow developers to change the options regardless of the stored values
		$this->options = apply_filters( 'query_recorder_options', $this->options );		
	}

	function update_options() {
		$_POST = stripslashes_deep( $_POST );

		$this->options['saved_queries_file_path'] = trim( $_POST['saved_queries_file_path'] );
		$this->options['exclude_queries'] = explode( "\n", trim( $_POST['exclude_queries'] ) );
		$this->options['record_queries_beggining_with'] = $_POST['record_queries_beggining_with'];

		update_option( 'query_recorder', $this->options );

		echo '<div id="message" class="updated fade"><p>' . __( 'Options saved.', 'query-recorder' ) . '</p></div>';
	}

	function page_options() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'query_recorder_update_options' );
			$this->update_options();
		}

		extract( $this->options, EXTR_SKIP );

		// these types of queries can be recorded, others cannot
		$recordable_queries = apply_filters( 'query_recorder_recordable_queries', array( 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE' ) );

		// process the content for the "Exclude queries containing" textarea
		$exclude_queries = ( empty( $exclude_queries ) ) ? '' : implode( "\n", $exclude_queries );

		require_once $this->plugin_dir_path . 'template/options.php';
	}

}
