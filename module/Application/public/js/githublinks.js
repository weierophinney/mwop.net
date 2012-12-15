// This defines Application.githublinks
define(["dojo/request", "dojo/dom-construct", "dojo/domReady!"], function(request, domConstruct){
    request.get("/github/links.xhr").then(
        function(content) {
            domConstruct.place(content, "github", "only");
        },
        function(error) {
            console.log("Unable to fetch github links: " + error);
        }
    );
});
