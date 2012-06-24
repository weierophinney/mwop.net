/* 
 * Based on 
 *     http://dojotoolkit.org/documentation/tutorials/1.7/jsonp/ 
 * by Dustin Machi
 *
 * and
 *     http://lupomontero.e-noise.com/blog/fetching-tweets-with-jquery-and-the-twitter-json-api
 *
 * by Lupo Montero
 */
require(["dojo/io/script", "dojo/dom", "dojo/_base/array", "dojo/domReady!"],
function(ioScript, dom, arrayUtil) {
    ioScript.get({
        url: "http://search.twitter.com/search.json",
        content: {q: "from:weierophinney", rpp: 30},
        callbackParamName: "callback"
    }).then(function(data) {
        return data.results;
    }).then(function(results) {
        var tweets = [];
        var count  = 0;
        arrayUtil.some(results, function(item, index) {
            var replyRe = /^@/;
            var matched = replyRe.exec(item.text);
            if (matched) {
                return true;
            }

            var date_tweet = new Date(item.created_at);
            var date_now   = new Date();
            var date_diff  = date_now - date_tweet;
            var hours      = Math.round(date_diff/(1000*60*60));
            var tweet;

            tweet  = '<li><a href="http://www.twitter.com/weierophinney/status/' + item.id_str + '">';
            tweet += item.text;
            tweet += "</a> (" + hours + " hours ago)</li>";
            tweets.push(tweet);

            count += 1;
            if (5 >= count) {
                return false;
            }
        });
        dom.byId("tweets").innerHTML = tweets.join("");
    });
});
