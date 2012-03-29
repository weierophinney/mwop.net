// This defines Application.scroll
define(["dojo/dom", "dojo/domReady!"], function(dom){
    /*
    * Normalized hide address bar for iOS & Android
    * (c) Scott Jehl, scottjehl.com
    * MIT License
    */
    var doc = dom.document;
    
    // If there's a hash, or addEventListener is undefined, stop here
    if (!location.hash && dom.addEventListener) {
        
        //scroll to 1
        window.scrollTo( 0, 1 );
        var scrollTop = 1,
            getScrollTop = function() {
                return dom.pageYOffset || doc.compatMode === "CSS1Compat" && doc.documentElement.scrollTop || doc.body.scrollTop || 0;
            },
        
            //reset to 0 on bodyready, if needed
            bodycheck = setInterval(function() {
                if (doc.body) {
                    clearInterval( bodycheck );
                    scrollTop = getScrollTop();
                    dom.scrollTo( 0, scrollTop === 1 ? 0 : 1 );
                }
            }, 15);
        
        dom.addEventListener("load", function(){
            setTimeout(function(){
                //at load, if user hasn't scrolled more than 20 or so...
                if (getScrollTop() < 20) {
                    //reset to hide addr bar at onload
                    dom.scrollTo(0, scrollTop === 1 ? 0 : 1);
                }
            }, 0);
        });
    }
});
