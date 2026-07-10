(function( $ ) {
	"use strict";
	
	$(document).ready(function() {
		$('#rankingcoach-scan-button').on('click', function(e) {
			e.preventDefault();
			var $button = $(this);
			
			$button.prop('disabled', true).text(RankingCoachDashboardWidget.scanningText);
			
			$.ajax({
				url: RankingCoachDashboardWidget.restUrl,
				type: 'POST',
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', RankingCoachDashboardWidget.nonce);
				},
				success: function(response) {
					location.reload();
				},
				error: function(xhr, status, error) {
					location.reload();
				}
			});
		});
	});
	
})(jQuery);
