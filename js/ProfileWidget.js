// Wrap code with module pattern
var ProfileWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeProfileWidget = function(parentElement)
    {
        ////////////////////////
        /////    Fields    /////
        ////////////////////////

        var container = parentElement;

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////

        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        container
        .append($("<p><a href=\"./userDetails.php\">Update Your Info</a>"));
        container.append($("<p><a href=\"../course_management?username="
        + $("#user").html() + "\">View Your Courses</a>"));
        container.append($("<p><a href=\"../topic_management?username="
        + $("#user").html() + "\">View Your Topics</a>"));

        /////////////////////////////
        // Public Instance Methods //
        /////////////////////////////
        return {
            getRootEl : function()
            {
                return container;
            },
            update : function()
            {

            },
            log : function(message)
            {

            }
        };
    };
}();

$(document).ready(function()
{
    profileWidget = makeProfileWidget($("#content"));
});