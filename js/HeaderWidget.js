// Wrap code with module pattern
var HeaderWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeHeaderWidget = function(parentElement)
    {
        ////////////////////////
        /////    Fields    /////
        ////////////////////////

        var container = parentElement;

        var urls = {
            companyIMG : "http://kelvin.ist.rit.edu/~axa9070/756/project1/images/companyLogo.png",
            profileUrl : "http://kelvin.ist.rit.edu/~axa9070/756/project1/profile.php",
        };

        var headerBar = $("<div id='header-inner'>");

        var logo = $("<div id='logo'>").append(
        $("<img title='GeneTees!' id='_logo' alt='companyLogo' />")
        .attr("src", urls.companyIMG));

        var title = $("#title");

        var options = $("<span id='options'>");

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function adjustHeaderTitle()
        {
            $("#title").css("left",
            "calc( 50% - " + $("#title").width() / 2 + "px )");
            $("#title").css("left",
            "-moz-calc( 50% - " + $("#title").width() / 2 + "px )");
            $("#title").css("left",
            "-webkit-calc( 50% - " + $("#title").width() / 2 + "px )");
        }
        ;

        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        container.append(headerBar);

        headerBar.append(logo).append(title).append(options);

        $("#title").change(adjustHeaderTitle).change();

        if ($(container).hasClass("linkProfile"))
        {
            $("img#_logo").wrap($("<a>").attr('href', urls.profileUrl)).hover(
            function()
            {
                $(this).parent().parent().fadeTo(50, 0.45);
            }, function()
            {
                $(this).parent().parent().fadeTo(50, 1.0);
            }).attr('title', 'Back to Profile');
        }

        /////////////////////////////
        // Public Instance Methods //
        /////////////////////////////
        return {
            getRootEl : function()
            {
                return container;
            },
            addOption : function(element)
            {
                if (!element.length)
                {
                    return;
                }

                options.prepend(element);

                if (options.children().length > 1)
                {
                    element.after(" | ");
                }

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
    headerWidget = makeHeaderWidget($("#header"));

    // Optional Header Options
    var manageUsersOption = $("#userManagementOption");
    var productManagementOption = $("#productManagementOption");
    var loginOption = $("#loginOption");
    var logoutOption = $("#logoutOption");
    var viewCartOption = $("#viewCartOption");
    var continueShoppingOption = $("#continueShoppingOption");

    headerWidget.addOption(loginOption);
    headerWidget.addOption(logoutOption);
    headerWidget.addOption(productManagementOption);
    headerWidget.addOption(manageUsersOption);
    headerWidget.addOption(viewCartOption);
    headerWidget.addOption(continueShoppingOption);
});