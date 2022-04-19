var disqus_config = Object.assign({}, document.getElementById('#disqus_thread').dataset);

(function(key) {
  var d = document, s = d.createElement('script');

  s.src = 'https://' + key + '.disqus.com/embed.js';

  s.setAttribute('data-timestamp', +new Date());
  (d.head || d.body).appendChild(s);
})(disqus_config.key);
