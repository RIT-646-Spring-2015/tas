// Wrap code with module pattern
var UserManagementWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeUserManagementWidget = function(parentElement)
    {
        //////////////////
        ///// Fields /////
        //////////////////

        var container = parentElement;

        var userTable = $("<table id='niceTable'><thead><tr id='niceTableHeader'>");

        var deleteUsersButton = $("<input id='deletedUsers' type='button' value='Delete Selected Users'>");

        var updateUserButton = $("<input id='updateUser' type='button' value='Update Selected User'>");

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function retrieveUsers(localAccess)
        {
            $("#niceTable tbody tr").remove();

            $.ajax({
                async : false,
                url : "retrieveUsers.php",
                type : 'POST'
            }).done(
            function(users)
            {
                $.each( users,
                function(username, user)
                {
                    authorities = $("<td id='Authorities'>");

                    $(user.authorities).each(function(i, authority)
                    {
                        authorities.append($("<p>").html(authority));
                    });

                    $("#niceTable").append(
                    $("<tr class='userRow' user='" + username + "'>")
                    .append(
                    $("<td>").append(
                    $("<span>" + username + "</span>").prepend(
                    $("<input class='selectUser' user='" + username
                    + "' type='checkbox'>")))).append(
                    $("<td><span>" + user.enabled + "</span></td>")).append(
                    $("<td><span>" + user.firstName + "</span></td>")).append(
                    $("<td><span>" + user.lastName + "</span></td>")).append(
                    $("<td><span>" + user.email + "</span></td>")).append(
                    $("<td><span>" + user.dateJoined + "</span></td>")).append(
                    $("<td><span>" + user.lastOnline + "</span></td>")).append(
                    authorities));
                });

                ////////////////////
                // USER SELECTION //
                ////////////////////
                $(".selectUser").click(function(event)
                {
                    event.stopPropagation();
                    var user = $(this).attr("user");
                    $(".userRow[user='" + user + "']").toggleClass("selected");
                    updateClickabilityOfButtons();
                });

                $(".userRow").children().click(function()
                {
                    var user = $(this).parent().attr("user");
                    $(".selectUser[user='" + user + "']").click();
                });

                if (localAccess === true )
                {
                    $("#niceTable").tablesorter({
                        sortList : [ [ 0, 0 ] ]
                    });
                }

                $("#niceTable").trigger("update");

            });

            updateClickabilityOfButtons();
        }

        function deleteUsers()
        {
            if (confirm("Are you sure you want to delete these users?"))
            {
                $
                .each(
                _.map($(".selectUser:checked"), function(checkbox)
                {
                    var user = $(checkbox).attr("user");
                    return $(".userRow[user=" + user + "]");
                }),
                function(i, user)
                {
                    if ($(user).children("#Authorities:contains(ADMIN)").length)
                    {
                        alert("You can't delete an admin!");
                    } else
                    {
                        $.ajax({
                            async : false,
                            url : "./deleteUser.php",
                            data: {username : $(user).attr("user") },
                            type : "POST"
                        }).done(retrieveUsers);
                    }
                });
            }
        }

        function updateUser()
        {
            var user = $(".selectUser:checked").attr("user");
            location = "userDetails.php?username=" + user;
        }

        function updateClickabilityOfButtons()
        {
            switch ($(".selectUser:checked").length)
            {
                case 0:
                    deleteUsersButton.prop("disabled", true);
                    updateUserButton.prop("disabled", true);
                    break;
                case 1:
                    deleteUsersButton.prop("disabled", false);
                    updateUserButton.prop("disabled", false);
                    break;
                default:
                    deleteUsersButton.prop("disabled", false);
                    updateUserButton.prop("disabled", true);
            }
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        container.append($("<div>").append(userTable).attr("id",
        "nice_tableBlock"));

        $("#niceTableHeader").append($("<th class='header'>Username</th>"))
        .append($("<th class='header'>Enabled</th>")).append(
        $("<th class='header'>First Name</th>")).append(
        $("<th class='header'>Last Name</th>")).append(
        $("<th class='header'>Email</th>")).append(
        $("<th class='header'>Date Joined</th>")).append(
        $("<th class='header'>Last Online</th>")).append(
        $("<th class='header'>Authorities</th>"));

        $("#niceTable").append($("<tbody>"));

        retrieveUsers(true);

        $('.userRow').on( 'contextmenu', function(e) {
            e.preventDefault();
            if ( confirm( 'See user\'s cart?' ) )
            {
                location = '../cart.php?username=' + $(this).attr('user');
            }
        });

        ///////////////////////
        // USER MODIFICATION //
        ///////////////////////
        deleteUsersButton.click(deleteUsers);

        updateUserButton.click(updateUser);

        $("#nice_tableBlock").append(deleteUsersButton)
        .append(updateUserButton);

        /////////////////////////////
        // Public Instance Methods //
        /////////////////////////////
        return {
            getRootEl : function()
            {
                return container;
            },
            refresh : function()
            {
                retrieveUsers(false);
            },
            log : function(message)
            {

            }
        };
    };
}();

$(document).ready(function()
{
    userManagementWidget = makeUserManagementWidget($("#content"));
});