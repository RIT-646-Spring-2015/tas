// Wrap code with module pattern
var ProductDetailsWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeProductDetailsWidget = function(parentElement)
    {
        ////////////////////////
        /////    Fields    /////
        ////////////////////////

        var container = parentElement;

        var productDetails;

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function getDetails()
        {
            var productId = $("#productId").html();

            productDetails = $.ajax({
                url : "./getProduct.php",
                type : "POST",
                data : {
                    productId : productId
                },
                async : false
            }).done(function(product)
            {
                // Update fields
                $(_.keys(product)).each(function()
                {
                    if ($("#" + this).attr("type") == "checkbox")
                    {
                        $("#" + this).prop("checked", product[this] === 'true');
                    }
                    else if( this == "price" || this == "sale" )
                    {
                        $("#" + this).val(product[this]/100.0);
                        console.log("UPDATED " + this);
                        formWidget.update();
                        product[this] = $("#" + this).val();
                    }
                    else if ($("#" + this).attr("type") == "file")
                    {
                        $("#" + this).parent().prepend("<a href='" + product.imageSitePath +
                                "'><p><img  style='float: left;' height='95' width='120' src='"  +
                                    product.imageSitePath + "'></img></p></a>");
                    }
                    else if (this == "description" )
                    {
                        $("#" + this).html(product[this]);
                    }
                    else
                    {
                        $("#" + this).val(product[this]);
                        console.log("UPDATED " + this);
                    }
                });
            }).responseJSON;
        }

        function updateProductDetails()
        {
            var field = $(this).attr("id");

            var detail = productDetails[field];

            if (field == "onSale")
            {
                if ($("#onSaleRow").is(".permanent"))
                    return;

                detail = detail === 'true';
                $("tr:has(#" + field + ")").toggleClass("selected",
                detail != $(this).is(":checked"));
            } 
            else
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
        getDetails(productDetails);

        $("tr input[type!=button]").on("change input", updateProductDetails);
        
        $("tr textarea").on("change input", updateProductDetails);

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
    productDetailsWidget = makeProductDetailsWidget($("#content"));
});