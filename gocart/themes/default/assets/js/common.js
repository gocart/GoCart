$(document).ready(function () {

		// Typeahead AJAX search
		var labels, mapped;
		$('.search-query').typeahead({
			minLength: 3,
			source: function(query, process) {
				$.get('cart/ajax_search', { query: query, limit: 10 }, function(data) {
					labels = []
					mapped = {}
					$.each(data, function (i, item) {
						mapped[item.name] = item.slug
						labels.push(item.name)
					})
					process(labels)
				}, 'json');
			},
			updater: function (item) {
				document.location = mapped[item];
				return item;
			}
		});

});