(function ($) {
  'use strict';

  $('#modal-instagram')
    .on('show.bs.modal', function(event) {
      let image = $(event.relatedTarget).data('instagram-image');
      let url   = $(event.relatedTarget).data('instagram-url');
      let html  = '<a href="' + url + '"><img class="img-fluid" src="' + image + '"></a><br /><em>Click image to see post on Instagram</em>';
      $(this).find('.modal-body').html(html);
    });
})(jQuery);
