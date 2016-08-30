(function ($, lunr) {
  'use strict';
  var index, links, resultdiv;

  $(document).ready(function () {
    $.getJSON('/js/search_terms.json', prepare);
  });

  function prepare (response) {
    index = lunr.Index.load(response.index);
    links = response.links;
    resultdiv = $('div.searchresults');
    $('input.search').on('keyup', search);
  }

  function search () {
    var item;

    // Get query value
    /* jshint validthis:true */
    var query = $(this).val();

    // Search for query value
    var result = index.search(query);

    if (result.length === 0) {
      // Hide results; none available
      resultdiv.hide();
      return;
    }

    // Show results
    resultdiv.empty();
    for (var i in result) {
      item = result[i];
      resultdiv.append('<a class="list-group-item" href="' + item.ref + '">' + links[item.ref].title + '</a>');
    }
    resultdiv.removeClass('hidden');
    resultdiv.show();
  }
})(jQuery, lunr);
