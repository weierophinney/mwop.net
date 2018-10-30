(function ($) {
  'use strict';
  var headers,
    resultdiv;

  resultdiv = $('div.searchresults');

  headers = new Headers();
  headers.append('Accept', 'application/json');

  function search(e) {
    var data,
      item,
      queryString,
      url;

    queryString = new URLSearchParams('');
    queryString.set('q', $(this).val());
    url = '/search?' + queryString.toString();

    data = {
      method: 'GET',
      headers: headers,
      mode: 'cors',
      cache: 'default'
    };

    fetch(url, data)
      .then(function(response) {
        if (! response.ok) {
          throw new Error('Invalid response from search endpoint');
        }
        return response.json();
      })
      .then(function(payload) {
        if (payload.length === 0) {
          resultdiv.addClass('invisible');
          return;
        }

        resultdiv.empty();
        resultdiv.append('<a class="list-group-item center-block search-close">[ CLOSE ]</a>');
        for (var i in payload) {
          item = payload[i];
          resultdiv.append('<a class="list-group-item" href="' + item.link + '">' + item.title + '</a>');
        }
        resultdiv.removeClass('invisible');
        $('a.search-close').on('click', function(){
          resultdiv.addClass('invisible');
        });
      });
  }

  $('input.search').on('keyup', search);
})(jQuery);
