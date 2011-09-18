dojo.provide("Blog.blog");

dojo.require("dojox.highlight");
dojo.require("dojox.highlight.languages._all");
dojo.require("dojox.highlight.languages.pygments.css");
dojo.addOnLoad(function() {
    dojo.query("div.example pre code").forEach(dojox.highlight.init);
});
