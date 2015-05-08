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

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function getDetails()
        {
            var courseNumber = $("#courseNumber").html();

            courseDetails = $.ajax({
                url : "./getCourse.php",
                type : "POST",
                data : {
                    courseNumber : courseNumber
                },
                async : false
            }).done(function(course)
            {
                // Update fields
                $(_.keys(course)).each(function()
                {
                    if ($("#" + this).attr("type") == "checkbox")
                    {
                        $("#" + this).prop("checked", course[this] === 'true');
                    } else if ($("#" + this).is("table"))
                    {
                        parseAuthorities(course);
                    } else
                    {
                        if (this != "password")
                        {
                            $("#" + this).val(course[this]);
                            console.log("UPDATED " + this);
                        }
                    }
                });
            }).responseJSON;
        }

        function updateCourseDetails()
        {
            var field = $(this).attr("id");

            var detail = courseDetails[field];

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
        getDetails(courseDetails);

        $("tr input[type!=button]").on("change input", updateCourseDetails);

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
    courseDetailsWidget = makeCourseDetailsWidget($("#content"));
});