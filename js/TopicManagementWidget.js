// Wrap code with module pattern
var TopicManagementWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeTopicManagementWidget = function(parentElement)
    {
        //////////////////
        ///// Fields /////
        //////////////////

        var container = parentElement;

        var user = $("#user").html() == "" ? undefined : $("#user").html();

        var courseNumber = $("#courseNumber").html() == "" ? undefined : $(
        "#courseNumber").html();

        var topicTable = $("<table id='niceTable'><thead><tr id='niceTableHeader'>");

        var deleteTopicsButton = $("<input id='deleteTopics' type='button' value='Delete Selected Topics'>");

        var updateTopicButton = $("<input id='updateTopic' type='button' value='Update Selected Topic'>");

        var addTopicButton = $("<input id='addTopic' type='button' value='Add New Topic'>");

        var tableHeaders = [ 'Topic Name', 'Username', 'Course Number', 'Link',
            'Submission Date', 'Status', 'Blacklisted' ];

        var topicRowTemplate = _
        .template("<tr class='topicRow<%= blacklisted? ' blacklisted':'' %>' topic='<%= name %>'> \
               <td> \
                 <input class='selectTopic' topic='<%= name %>' type='checkbox'><br> \
                 <span><%= name %></span> \
               </td> \
               <td> \
                 <span><%= user.username %></span><br><span class='userFullName'><%= user.fullName %></span> \
               </td> \
               <td> \
                 <span><%= courseNumber %></span> \
               </td> \
               <td> \
                 <span class='link'><a href=\"<%= link %>\"><%= link %></a></span> \
               </td> \
               <td> \
                 <span><%= submissionDate %></span> \
               </td> \
               <td> \
                 <span class='status_<%= status %>'><%= status %></span> \
               </td> \
               <td> \
                 <span><%= blacklisted == 0? false:true %></span> \
               </td></tr>");

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function retrieveTopics(localAccess)
        {
            $("#niceTable tbody tr").remove();

            $.ajax({
                async : false,
                url : "retrieveTopics.php",
                type : 'POST',
                data : {
                    username : user,
                    courseNumber : courseNumber
                }
            }).done(
            function(topics)
            {
                if (topics.length <= 0)
                    return;

                $.each(topics, function(topicName, topic)
                {
                    $("#niceTable").append(topicRowTemplate(topic));
                });

                //////////////////////
                // TOPIC SELECTION //
                //////////////////////
                $(".selectTopic").click(
                function(event)
                {
                    event.stopPropagation();
                    var topic = $(this).attr("topic");
                    $(".topicRow[topic='" + topic + "']").toggleClass(
                    "selected");
                    updateClickabilityOfButtons();
                });

                $(".topicRow").children().click(function()
                {
                    var topic = $(this).parent().attr("topic");
                    $(".selectTopic[topic='" + topic + "']").click();
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

        function deleteTopics()
        {
            if (confirm("Are you sure you want to delete these topics?"))
            {
                $.each(_.map($(".selectTopic:checked"), function(checkbox)
                {
                    var topicName = $(checkbox).attr("topic");
                    return $(".topicRow[topic='" + topicName + "']");
                }), function(i, topic)
                {
                    $.ajax({
                        async : false,
                        url : "deleteTopic.php",
                        data : {
                            topicName : $(topic).attr("topic")
                        },
                        type : "POST"
                    }).done(retrieveTopics);
                });
            }
        }

        function updateTopic()
        {
            var topicName = $(".selectTopic:checked").attr("topic");
            location = "topicDetails.php?topicName=" + topicName;
        }

        function updateClickabilityOfButtons()
        {
            switch ($(".selectTopic:checked").length)
            {
                case 0:
                    deleteTopicsButton.prop("disabled", true);
                    updateTopicButton.prop("disabled", true);
                    break;
                case 1:
                    deleteTopicsButton.prop("disabled", false);
                    updateTopicButton.prop("disabled", false);
                    break;
                default:
                    deleteTopicsButton.prop("disabled", false);
                    updateTopicButton.prop("disabled", true);
            }
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        container.append($("<div>").append(topicTable).attr("id",
        "nice_tableBlock"));

        $.each(tableHeaders, function()
        {
            $("#niceTableHeader").append(
            $("<th class='header'>" + this + "</th>"));
        });

        $("#niceTable").append($("<tbody>"));

        retrieveTopics(true);

        //////////////////////////
        // TOPIC MODIFICATION //
        //////////////////////////
        deleteTopicsButton.click(deleteTopics);

        updateTopicButton.click(updateTopic);

        addTopicButton.click(function()
        {
            location = 'addTopic.php?' + ((user) ? ('username=' + user) : '')
            + ((courseNumber) ? ('courseNumber=' + courseNumber) : '');
        });

        $("#nice_tableBlock").append(deleteTopicsButton).append(
        updateTopicButton).append(addTopicButton);
        
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
                retrieveTopics(false);
            },
            log : function(message)
            {

            }
        };
    };
}();

$(document).ready(function()
{
    topicManagementWidget = makeTopicManagementWidget($("#content"));
});