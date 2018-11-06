(function ($) {
  'use strict';

  const headers = new Headers();
  headers.append('Accept', 'application/json');

  const search = (query, callback) => {
    const queryString = new URLSearchParams('');
    queryString.set('q', query);

    const url = '/search?' + queryString.toString();

    const data = {
      method: 'GET',
      headers: headers,
      mode: 'cors',
      cache: 'default'
    };

    fetch(url, data)
      .then((response) => {
        if (! response.ok) {
          throw new Error('Invalid response from search endpoint');
        }
        return response.json();
      })
      .then((payload) => {
        callback(payload);
      });
  };

  $('#searchinput')
    .autocomplete({}, [{
			source: search,
			displayKey: 'title'
		}])
    .on('autocomplete:selected', (event, suggestion, dataset) => {
      window.location.href = suggestion.link;
    });
})(jQuery);
