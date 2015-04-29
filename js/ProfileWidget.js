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
        container.append($("<p><a href=\"./\">Go Shopping</a>"));
        container.append($("<p><a href=\"./user_management/userDetails.php?username=" + $('#user').html() + "\">Update Your Info</a>"));
        container.append($("<p><a href=\"./cart.php\">View Your Cart</a>"));

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