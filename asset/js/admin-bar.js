(function($) {

	function update_title( active_status ) {
		if( '1' == active_status ) {
			$('#wp-admin-bar-query-recorder').attr('title', query_recorder.stop_recording);
			$('#wp-admin-bar-query-recorder').addClass('active');
		} else {
			$('#wp-admin-bar-query-recorder').attr('title', query_recorder.start_recording);
			$('#wp-admin-bar-query-recorder').removeClass('active');
		}
	}

	function alert_error( active_status ) {
		if( '1' == active_status ) {
			alert( query_recorder.ajax_problem_off );
		} else {
			alert( query_recorder.ajax_problem_on );
		}
	}

	$(document).ready(function() {

		active_status = query_recorder.active;
		update_title( active_status );

		$('#wp-admin-bar-query-recorder').click(function() {
			if( $(this).hasClass('doing-ajax') ) return;

			var spin_opts = {
				lines: 9, // The number of lines to draw
				length: 3, // The length of each line
				width: 2, // The line thickness
				radius: 2, // The radius of the inner circle
				corners: 1, // Corner roundness (0..1)
				rotate: 0, // The rotation offset
				direction: 1, // 1: clockwise, -1: counterclockwise
				color: '#fff', // #rgb or #rrggbb or array of colors
				speed: 1, // Rounds per second
				trail: 60, // Afterglow percentage
				shadow: false, // Whether to render a shadow
				hwaccel: false, // Whether to use hardware acceleration
				className: 'qr-spinner', // The CSS class to assign to the spinner
				zIndex: 2e9, // The z-index (defaults to 2000000000)
				top: '50%', // Top position relative to parent
				left: '50%' // Left position relative to parent
			};

			$(this).addClass('doing-ajax');
			$(this).spin(spin_opts);

			$.ajax({
				url: 		query_recorder.ajax_url,
				type: 		'POST',
				dataType:	'text',
				cache: 	false,
				data: {
					action 			: 'query_recorder_toggle_active',
					active_status	: active_status
				},
				error: function(jqXHR, textStatus, errorThrown){
					$('#wp-admin-bar-query-recorder').removeClass('doing-ajax');
					$('#wp-admin-bar-query-recorder').spin(false);
					alert_error( active_status );
				},
				success: function(data){
					data = $.trim( data );
					$('#wp-admin-bar-query-recorder').removeClass('doing-ajax');
					$('#wp-admin-bar-query-recorder').spin(false);
					if( data == '-1' ) {
						alert_error( active_status );
						return;
					}

					active_status = ( '1' == active_status ) ? 0 : 1;
					update_title( active_status );
				}
			});
		});

	});

})(jQuery);