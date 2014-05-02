<div class="wrap query-recorder">
	<h2><?php _e( 'Query Recorder Settings', 'query-recorder' ); ?></h2>

	<?php if( isset( $_GET['settings-updated'] ) ) : ?>
		<div id="message" class="updated fade"><p><strong><?php _e( 'Settings saved.', 'query-recorder' ); ?></strong></p></div>
	<?php endif; ?>

	<div class="query-recorder-container">

		<div class="query-recorder-main">

			<p class="query-recorder-intro"><?php _e( "When you're ready to start recording queries, simply click the little record button in the admin bar above. It will turn red to show you are currently recording queries. Click it again to turn it off.", 'query-recorder' ); ?></p>

			<form method="post" action="">
				<?php wp_nonce_field( 'query_recorder_update_options' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="saved-queries-file-path"><?php _e( "Save queries to file", "query-recorder" ); ?></label>
							</th>
							<td>
								<input type="text" class="regular-text" value="<?php echo $saved_queries_file_path; ?>" id="saved-queries-file-path" name="saved_queries_file_path" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="exclude-queries"><?php _e( "Don't record queries containing", "query-recorder" ); ?></label>
							</th>
							<td>
								<p>
									<textarea id="exclude-queries" name="exclude_queries"><?php echo $exclude_queries; ?></textarea>
									<p class="description"><?php _e( "One exclusion per line.", "query-recorder" ); ?></p>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e( "Record queries that begin with", "query-recorder" ); ?>
							</th>
							<td>
								<fieldset>
									<?php foreach( $recordable_queries as $keyword ) : ?>
										<?php $keyword_lower = strtolower( $keyword ); ?>
										<label for="<?php echo $keyword_lower; ?>">
											<input type="checkbox" value="<?php echo $keyword; ?>" id="<?php echo $keyword_lower; ?>" name="record_queries_beggining_with[]"<?php echo ( !empty( $record_queries_beggining_with ) && in_array( $keyword, $record_queries_beggining_with ) ) ? ' checked="checked"' : ''; ?> />
											<span class="code"><?php echo $keyword; ?></span>
										</label><br />
									<?php endforeach; ?>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button(); ?>
			</form>

		</div>

		<?php require_once $this->plugin_dir_path . 'template/sidebar.php'; ?>

	</div>
</div>