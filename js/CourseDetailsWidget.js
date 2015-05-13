// Wrap code with module pattern
var CourseDetailsWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeCourseDetailsWidget = function(parentElement)
    {
        ////////////////////////
        /////    Fields    /////
        ////////////////////////

        var container = parentElement;

        var courseDetails;

        var roles = [];

        var userRemoval = [];

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function getDetails()
        {
            roles = [];
            userRemoval = [];
            var courseNumber = $("#courseNumber").html();

            courseDetails = $
            .ajax({
                url : "./getCourse.php",
                type : "POST",
                data : {
                    courseNumber : courseNumber
                },
                async : false
            })
            .done(
            function(course)
            {
                // Update fields
                $(_.keys(course))
                .each(
                function()
                {
                    if (this == "enrolled")
                    {
                        $("#studentsEnrolled").empty();
                        $.each(course.enrolled, function(role, users)
                        {
                            roles.push(role);
                            $("#studentsEnrolled").append(
                            "<h4 class='roleHeader'>" + role + "</h4>");

                            $.each(users, function(username, user)
                            {
                                $("#studentsEnrolled").append(
                                "<div><input class='userRemove' type='checkbox' user='"
                                + username + "'><p class='roster' user='"
                                + username + "'>" + username + " "
                                + user.fullName + "<br>" + user.email
                                + "</p></div>");
                            });

                        });
                    } else if (this == "topics")
                    {
                        $("#topics table tr:not(:has(th))").empty();
                        var topicRowTemplate = _
                        .template("<tr><td><%= username %></td>\
                        		       <td><%= topic %></td>\
                        		       <td class='status_<%= status %>'><%= status %></td></tr>");

                        $.each(course.topics, function(username, topic)
                        {
                            $("#topics table").append(topicRowTemplate(topic));
                        });

                    } else
                    {
                        $("#" + this).val(course[this]);
                        console.log("UPDATED " + this);
                    }
                    $('p.roster').each(
                    function()
                    {
                        $(this).click(
                        function(e)
                        {
                            e.stopImmediatePropagation();
                            $("input[user='" + $(this).attr("user") + "'")
                            .click();
                        });
                    });
                });
                $("tr input[type!=button]").on("change input",
                updateCourseDetails);
                updateClickabilityOfButtons();
            }).responseJSON;
        }

        function updateCourseDetails()
        {
            var field = $(this).attr("id");

            var detail = courseDetails[field];

            if ($(this).is(".userRemove"))
            {
                if ($(this).prop("checked"))
                {
                    userRemoval.push($(this).attr("user"));
                } else
                {
                    userRemoval = $.grep(userRemoval, function(username)
                    {
                        return $(this).attr("user") == username;
                    });
                }
            } else
            {
                $("tr:has(#" + field + ")").toggleClass("selected",
                detail != $(this).val());
            }

            updateClickabilityOfButtons();
        }

        function updateClickabilityOfButtons()
        {
            var clickable = $(".selected").length > 0;

            $("#updateFieldsButton").prop("disabled", !clickable);
            $("#removeUsersButton").prop("disabled", userRemoval.length <= 0);
        }

        function addUsers()
        {
            if ($("div#userList").length > 0)
            {
                $("div#userList").remove();
                return;
            }

            $("div#userList").remove();
            var courseNumber = $("#courseNumber").html();

            // Open a widget showing all users not already enrolled in this course
            container.append($("<div id='userList'>"));

            $.ajax({
                url : "../user_management/retrieveUsers.php",
                type : "POST",
                data : {
                    notInCourse : courseNumber
                },
                async : false
            }).done(
            function(users)
            {
                $.each(users, function(username, user)
                {
                    var rolesRadio = $("<p>");

                    $.each(roles, function(i, role)
                    {
                        rolesRadio
                        .append("<p>\
                        		<input id='"
                        + username + "_" + role
                        + "' type=radio name='role' user=" + username
                        + " role='" + role + "'><label for='" + username + "_"
                        + role + "'>" + role
                        + "</label> \
                        		</p>");
                    });

                    $("div#userList").append(
                    $(
                    "<div class='userAddBlock'><p class='userAdd' user='"
                    + username + "'>" + username + " (" + user.firstName + " "
                    + user.lastName + ")<br>&nbsp;&lt;" + user.email
                    + "&gt;</p></div>").append(rolesRadio));
                });

                $("p.userAdd").click(
                function()
                {
                    var username = $(this).attr("user");
                    var role = $("input[user=" + username + "]:checked").attr(
                    "role");

                    if (role == undefined)
                    {
                        alert("You must select a role for " + username
                        + " before adding the user to the course.");
                        return;
                    }

                    $.ajax({
                        url : "./addUserToCourse.php",
                        type : "POST",
                        data : {
                            username : username,
                            courseNumber : courseNumber,
                            role : role
                        }
                    }).done(function()
                    {
                        getDetails();
                        $("div#userList").remove();
                    }).fail(
                    function(message)
                    {
                        if (message.responseText
                        && message.responseText != "null")
                        {
                            alert(message.responseText);
                        }
                    });
                });
            });
        }

        function removeUsers()
        {
            $.each(userRemoval, function(i, username)
            {
                $.ajax({
                    url : "./removeUserFromCourse.php",
                    type : "POST",
                    data : {
                        username : username,
                        courseNumber : $("#courseNumber").html()
                    }
                }).done(function()
                {
                    getDetails();
                    $("div#userList").remove();
                });
            });
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        getDetails(courseDetails);

        $("input#addUsersButton").click(addUsers);
        $("input#removeUsersButton").click(removeUsers);

        // If it was meant to be permanent, disable it!
        $("tr.permanent input[type!=button][type!=submit]").prop("readonly",
        true);

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
    courseDetailsWidget = makeCourseDetailsWidget($("#content"));
});