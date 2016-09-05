if (navigator.serviceWorker) {
  navigator.serviceWorker.register('/service-worker.js', {
    scope: '/'
  });
}

(function ($) {
  $(document).ready(function() {
    $('#site-search').submit(function (event) {
      event.preventDefault();
      var terms = $('#site-search .search').val();
      var query = {
        q: 'site:mwop.net ' + terms
      };
      window.location.href = 'https://www.google.com/#' + $.param(query);
    });
  });
})(jQuery);
