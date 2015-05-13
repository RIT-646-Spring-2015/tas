// Wrap code with module pattern
var TopicDetailsWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeTopicDetailsWidget = function(parentElement)
    {
        ////////////////////////
        /////    Fields    /////
        ////////////////////////

        var container = parentElement;

        var topicDetails;

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function getDetails()
        {
            var topicName = $("#topicName").html();

            topicDetails = $.ajax({
                url : "./getTopic.php",
                type : "POST",
                data : {
                    topicName : topicName
                },
                async : false
            }).done(function(topic)
            {
                // Update fields
                $(_.keys(topic)).each(function()
                {
                    if ($("#" + this).attr("type") == "checkbox")
                    {
                        $("#" + this).prop("checked", topic[this] === 'true');
                    } else
                    {
                        $("#" + this).val(topic[this]);
                        console.log("UPDATED " + this);
                    }
                });
            }).responseJSON;
        }

        function updateTopicDetails()
        {
            var field = $(this).attr("id");

            var detail = topicDetails[field];

            if ($(this).attr("type") == "checkbox")
            {
                $("tr:has(#" + field + ")").toggleClass("selected",
                (detail === 'true') != $(this).prop('checked'));
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
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        getDetails(topicDetails);

        $("tr input[type!=button], tr select").on("change input",
        updateTopicDetails);

        $("tr textarea").on("change input", updateTopicDetails);

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
    topicDetailsWidget = makeTopicDetailsWidget($("#content"));
});