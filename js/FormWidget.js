// Wrap code with module pattern
var FormWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeFormWidget = function(parentElement)
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

        // Make Clear button work ;in a more custom way
        $("input[value='Clear']").click(function()
        {
            $('input[type!=button] input[type!=submit]').val('');
        });

        // Currency Formatter
        $('.currency').blur(function()
        {
            $('.currency').formatCurrency();
        });

        $('.currency').formatCurrency();
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
                $('.currency').formatCurrency();
            },
            log : function(message)
            {

            }
        };
    };
}();

$(document).ready(function()
{
    formWidget = makeFormWidget($("#content"));
});