// Wrap code with module pattern
var UserDetailsWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeUserDetailsWidget = function(parentElement)
    {
        ////////////////////////
        /////    Fields    /////
        ////////////////////////

        var container = parentElement;

        var userDetails;

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function getDetails()
        {
            var username = $("#user").html();
            parseAuthorities;

            userDetails = $.ajax({
                url : "./getUser.php",
                type : "POST",
                data : {
                    username : username
                },
                async : false
            }).done(function(user)
            {
                // Update fields
                $(_.keys(user)).each(function()
                {
                    if ($("#" + this).attr("type") == "checkbox")
                    {
                        $("#" + this).prop("checked", user[this] === 'true');
                    } else if ($("#" + this).is("table"))
                    {
                        parseAuthorities(user);
                    } else
                    {
                        if (this != "password")
                        {
                            $("#" + this).val(user[this]);
                            console.log("UPDATED " + this);
                        }
                    }
                });
            }).responseJSON;
        }

        var parseAuthorities = function(user)
        {
            // Show the permissions granted to a user
            $(user.authorities).each(function()
            {
                $("input[auth=" + this + "]").prop("checked", true);
            });
        }

        function changePassword()
        {
            $("#confirmPasswordRow").css("display", "table-row").addClass(
            "selected");
            $("#password").val("");
            $("label[for='password']").val("New Password");
            $("#passwordRow").addClass("selected");
            updateClickabilityOfButtons();
        }

        function updateUserDetails()
        {
            var field = $(this).attr("id");

            var detail = userDetails[field];

            if ($(this).is(".authBox"))
            {
                if ($("#authoritiesRow").is(".permanent"))
                    return;

                var unChanged = true;
                $(".authBox").each(
                function()
                {
                    p = _.contains(detail, $(this).attr(
                    "auth"));
                    q = this.checked;

                    return unChanged = ((!p || q) && (!q || p));
                });

                $("tr:has(#" + field + ")").toggleClass("selected", !unChanged);
            } else if ($(this).is("#enabled"))
            {
                if ($("#enabledRow").is(".permanent"))
                    return;

                detail = detail === 'true';
                $("tr:has(#" + field + ")").toggleClass("selected",
                detail != $(this).is(":checked"));
            } else
            {
                $("tr:has(#" + field + ")").toggleClass("selected",
                detail != $(this).val());
            }

            updateClickabilityOfButtons();
        }

        function updateClickabilityOfButtons()
        {
            var clickable = $(".selected").not("#passwordRow").length > 0;

            $("#updateFieldsButton").prop("disabled", !clickable);
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        getDetails(userDetails);

        $("tr input[type!=button]").on("change input", updateUserDetails);

        $("input#changePasswordButton").click(changePassword);

        // If it was meant to be permanent, disable it!
        $("tr.permanent input[type!=button][type!=submit]").prop("readonly", true);

        $("tr.permanent input[type=checkbox]").click(function()
        {
            return false;
        });

        updateClickabilityOfButtons();

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
    userDetailsWidget = makeUserDetailsWidget($("#content"));
});