var lunr     = require('lunr');
var jsonfile = require('jsonfile');
var data     = require('../data/search_terms.json');
var links    = {};

var index = lunr(function () {
  this.ref('id');
  this.field('title', {boost: 10});
  this.field('tags', {boost: 100});
  this.field('content');
});

data.docs.forEach(function (doc) {
  index.add(doc);
  links[doc.id] = { title: doc.title };
});

var terms = { index: index, links: links };

jsonfile.writeFile('public/js/search_terms.json', terms, function (err) {
  if (err) {
    console.error("An error occurred writing the index to the filesystem");
    console.error(err);
  }
  console.log("Done creating search index");
});
