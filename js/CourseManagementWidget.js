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

        var courseTable = $("<table id='niceTable'><thead><tr id='niceTableHeader'>");

        var deleteCoursesButton = $("<input id='deletedCourses' type='button' value='Delete Selected Courses'>");

        var updateCourseButton = $("<input id='updateCourse' type='button' value='Update Selected Course'>");

        var addCourseButton = $("<input id='addCourse' type='button' value='Add a Course'>");

        var tableHeaders = [ 'Course Number', 'Course Name', 'Roster' ];

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function retrieveCourses(localAccess)
        {
            $("#niceTable tbody tr").remove();

            $.ajax({
                async : false,
                url : "retrieveCourses.php",
                type : 'POST'
            }).done(
            function(courses)
            {
                $.each(courses, function(courseNumber, course)
                {
                    enrolled = $("<td id='Enrolled'>");

                    $.each(course.enrolled, function(role, usernames)
                    {
                        enrolled.append($("<p>").html(
                        role + ': ' + usernames.length));
                    });

                    $("#niceTable").append(
                    $("<tr class='courseRow' course='" + courseNumber + "'>")
                    .append(
                    $("<td>").append(
                    $("<span>" + courseNumber + "</span>").prepend(
                    $("<input class='selectCourse' course='" + courseNumber
                    + "' type='checkbox'>")))).append(
                    $("<td><span>" + course.name + "</span></td>")).append(
                    enrolled));
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

            });

            updateClickabilityOfButtons();
        }

        function deleteCourses()
        {
            if (confirm("Are you sure you want to delete these courses?"))
            {
                $
                .each(
                _.map($(".selectCourse:checked"), function(checkbox)
                {
                    var course = $(checkbox).attr("course");
                    return $(".courseRow[course=" + course + "]");
                }),
                function(i, course)
                {
                    if (!$(course).children("#Enrolled:empty").length)
                    {
                        alert("You can't delete a course with users enrolled!");
                    } else
                    {
                        $.ajax({
                            async : false,
                            url : "./deleteCourse.php",
                            data : {
                                courseNumber : $(course).attr("course")
                            },
                            type : "POST"
                        }).done(retrieveCourses);
                    }
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

        $("#nice_tableBlock").append(deleteCoursesButton).append(
        updateCourseButton).append(addCourseButton);

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