// Wrap code with module pattern
var CourseManagementWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeCourseManagementWidget = function(parentElement)
    {
        //////////////////
        ///// Fields /////
        //////////////////

        var container = parentElement;

        var user = $("#user").html() == "" ? undefined : $("#user").html();

        var courseTable = $("<table id='niceTable'><thead><tr id='niceTableHeader'>");

        var deleteCoursesButton = $("<input id='deletedCourses' type='button' value='Delete Selected Courses'>");

        var updateCourseButton = $("<input id='updateCourse' type='button' value='Update Selected Course'>");

        var addCourseButton = $("<input id='addCourse' type='button' value='Add a Course'>");

        var tableHeaders = [ 'Course Number', 'Course Name', 'Roster',
            'Proposed Topics' ];

        var courseRowTemplate = _
        .template("<tr class='courseRow' course='<%= courseNumber %>'> \
              <td> \
                <input class='selectCourse' course='<%= courseNumber %>' type='checkbox'> \
                <span><%= courseNumber %></span> \
              </td> \
              <td> \
                <span><%= name %></span> \
              </td> \
              <td id='enrolled'> \
                <% $.each( enrolled, function(role, usernames) { %> \
                  <p><%= role %>: <%= usernames.length %></p> \
                <% }); %> \
              </td> \
              <td id='topics'> \
                <% $.each( topics, function(topicName, username) { %> \
                  <p><%= topicName %>: <%= username %></p> \
                <% }); %> \
              </td></tr>");

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function retrieveCourses(localAccess)
        {
            $("#niceTable tbody tr").remove();

            $.ajax({
                async : false,
                url : "retrieveCourses.php",
                type : 'POST',
                data : {
                    username : user
                }
            }).done(
            function(courses)
            {
                if (courses.length <= 0)
                    return;

                $.each(courses, function(courseNumber, course)
                {
                    course.courseNumber = courseNumber;
                    $("#niceTable").append(courseRowTemplate(course));
                });

                //////////////////////
                // COURSE SELECTION //
                //////////////////////
                $(".selectCourse").click(
                function(event)
                {
                    event.stopPropagation();
                    var course = $(this).attr("course");
                    $(".courseRow[course='" + course + "']").toggleClass(
                    "selected");
                    updateClickabilityOfButtons();
                });

                $(".courseRow").children().click(function()
                {
                    var course = $(this).parent().attr("course");
                    $(".selectCourse[course='" + course + "']").click();
                });

                if (localAccess === true)
                {
                    $("#niceTable").tablesorter({
                        sortList : [ [ 0, 0 ] ]
                    });
                }

                $("#niceTable").trigger("update");

            }).fail(function(message)
            {
                console.error(message.responseText);
            });

            updateClickabilityOfButtons();
        }

        function deleteCourses()
        {
            if (confirm("Are you sure you want to delete these courses? \
                \n\nDoing so will: \
                \n    - Delete all submitted topics for this course \
                \n    - Remove all users from this course\n\n  Proceed?"))
            {
                $.each(_.map($(".selectCourse:checked"), function(checkbox)
                {
                    var course = $(checkbox).attr("course");
                    return $(".courseRow[course=" + course + "]");
                }), function(i, course)
                {
                    $.ajax({
                        async : false,
                        url : "./deleteCourse.php",
                        data : {
                            courseNumber : $(course).attr("course")
                        },
                        type : "POST"
                    }).done(retrieveCourses);
                });
            }
        }

        function updateCourse()
        {
            var course = $(".selectCourse:checked").attr("course");
            location = "courseDetails.php?courseNumber=" + course;
        }

        function addCourse()
        {
            location = "addCourse.php";
        }

        function updateClickabilityOfButtons()
        {
            switch ($(".selectCourse:checked").length)
            {
                case 0:
                    deleteCoursesButton.prop("disabled", true);
                    updateCourseButton.prop("disabled", true);
                    break;
                case 1:
                    deleteCoursesButton.prop("disabled", false);
                    updateCourseButton.prop("disabled", false);
                    break;
                default:
                    deleteCoursesButton.prop("disabled", false);
                    updateCourseButton.prop("disabled", true);
            }
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        container.append($("<div>").append(courseTable).attr("id",
        "nice_tableBlock"));

        $.each(tableHeaders, function()
        {
            $("#niceTableHeader").append(
            $("<th class='header'>" + this + "</th>"));
        });

        $("#niceTable").append($("<tbody>"));

        retrieveCourses(true);

        ///////////////////////
        // USER MODIFICATION //
        ///////////////////////
        deleteCoursesButton.click(deleteCourses);

        updateCourseButton.click(updateCourse);

        addCourseButton.click(addCourse)

        if (!user)
        {
            $("#nice_tableBlock").append(deleteCoursesButton).append(
            updateCourseButton).append(addCourseButton);
        } else
        {
            $("#nice_tableBlock").append(
            updateCourseButton.val("View Selected Course"));
        }

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
                retrieveCourses(false);
            },
            log : function(message)
            {

            }
        };
    };
}();

$(document).ready(function()
{
    courseManagementWidget = makeCourseManagementWidget($("#content"));
});