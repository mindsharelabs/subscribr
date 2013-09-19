// @todo move this to PHP so it can be easily filtered, etc.
jQuery.noConflict();
jQuery(document).ready(function() {
	emailSubscribeInit();
});

function emailSubscribeInit() {
	jQuery('.chosen-select').chosen({
		search_contains: true,
		width: '100%',
		placeholder_text_multiple: 'Select Email Subscriptions',
		no_results_text: 'No results'
	});
}
