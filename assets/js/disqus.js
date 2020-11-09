var disqus_config = function () {
  jQuery.extend(this.page, jQuery('#disqus_thread').data());
};

(function(key) {
  var d = document, s = d.createElement('script');

  s.src = 'https://' + key + '.disqus.com/embed.js';

  s.setAttribute('data-timestamp', +new Date());
  (d.head || d.body).appendChild(s);
})(jQuery('#disqus_thread').data('key'));
