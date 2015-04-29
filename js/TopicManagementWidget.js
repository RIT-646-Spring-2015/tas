// Wrap code with module pattern
var ProductManagementWidget = function()
{
    var global = this;

    /////////////////////////////////
    // Widget Constructor Function //
    /////////////////////////////////
    global.makeProductManagementWidget = function(parentElement)
    {
        //////////////////
        ///// Fields /////
        //////////////////

        var container = parentElement;

        var productTable = $("<table id='niceTable'><thead><tr id='niceTableHeader'>");

        var deleteProductsButton = $("<input id='deleteProducts' type='button' value='Delete Selected Products'>");

        var updateProductButton = $("<input id='updateProduct' type='button' value='Update Selected Product'>");

        var addProductButton = $("<input id='addProduct' type='button' value='Add New Product'>");

        //////////////////////////////
        // Private Instance Methods //
        //////////////////////////////
        function retrieveProducts(localAccess)
        {
            $("#niceTable tbody tr").remove();

            var products = paginationWidget.retrieveProducts( false, 0 );
            
            $.each( products,
            function(i, product)
            {
                product.Price /= 100;
                product.Sale /= 100;
                
                $("#niceTable").append(
                $("<tr class='productRow' title='" + product.Description +
                    "' product='" + product.ProductId + "'>")
                .append(
                $("<td>").append(
                $("<span>" + product.ProductId + "</span>").prepend(
                $("<input class='selectProduct' product='" + product.ProductId
                + "' type='checkbox'>")))).append(
                $("<td><img height='95' width='120' src='"  + product.ImageSitePath + "'></img></td>")).append(
                $("<td><span>"  + product.Name + "</span></td>")).append(
                $("<td><span class='currency'>" + product.Price + "</span></td>")).append(
                $("<td><span>"  + product.Quantity + "</span></td>")).append(
                $("<td><span>"  + (product.OnSale?'true':'false') + "</span></td>")).append(
                $("<td><span class='currency'>"  + product.Sale + "</span></td>")).append(
                $("<td><span class='currency'>"  + ((product.OnSale? product.Sale : product.Price) * product.Quantity) + "</span></td>")));
            });

            ///////////////////////
            // PRODUCT SELECTION //
            ///////////////////////
            $(".selectProduct").click(function(event)
            {
                event.stopPropagation();
                var productId = $(this).attr("product");
                $(".productRow[product='" + productId + "']").toggleClass("selected");
                updateClickabilityOfButtons();
            });

            $(".productRow").children().click(function()
            {
                var productId = $(this).parent().attr("product");
                $(".selectProduct[product='" + productId + "']").click();
            });

            if (localAccess === true )
            {
                $("#niceTable").tablesorter({
                    sortList : [ [ 0, 0 ] ]
                });
            }

            $("#niceTable").trigger("update");


            updateClickabilityOfButtons();
        }

        function deleteProducts()
        {
            if (confirm("Are you sure you want to delete these products?"))
            {
                $
                .each(
                _.map($(".selectProduct:checked"), function(checkbox)
                {
                    var productId = $(checkbox).attr("product");
                    return $(".productRow[product=" + productId + "]");
                }),
                function(i, product)
                {
                    $.ajax({
                        async : false,
                        url : "product_management/deleteProduct.php",
                        data: {productId : $(product).attr("product") },
                        type : "POST"
                    }).done(retrieveProducts);
                });
            }
        }

        function updateProduct()
        {
            var productId = $(".selectProduct:checked").attr("product");
            location = "product_management/productDetails.php?productId=" + productId;
        }

        function updateClickabilityOfButtons()
        {
            switch ($(".selectProduct:checked").length)
            {
                case 0:
                    deleteProductsButton.prop("disabled", true);
                    updateProductButton.prop("disabled", true);
                    break;
                case 1:
                    deleteProductsButton.prop("disabled", false);
                    updateProductButton.prop("disabled", false);
                    break;
                default:
                    deleteProductsButton.prop("disabled", false);
                    updateProductButton.prop("disabled", true);
            }
        }
        //////////////////////////////////////////
        // Find Pieces and Enliven DOM Fragment //
        //////////////////////////////////////////
        container.append($("<div>").append(productTable).attr("id",
        "nice_tableBlock"));

        $("#niceTableHeader").append($("<th class='header'>ProductId</th>"))
        .append($("<th class='header'>Product Image</th>")).append(
        $("<th class='header'>Product Name</th>")).append(
        $("<th class='header'>Retail Price</th>")).append(
        $("<th class='header'>Quantity in Stock</th>")).append(
        $("<th class='header'>On Sale</th>")).append(
        $("<th class='header'>Sale Price</th>")).append(
        $("<th class='header'>Potential Income</th>"));

        $("#niceTable").append($("<tbody>"));

        retrieveProducts(true);

        //////////////////////////
        // PRODUCT MODIFICATION //
        //////////////////////////
        deleteProductsButton.click(deleteProducts);

        updateProductButton.click(updateProduct);

        addProductButton.click( function(){ location = 'product_management/addProduct.php' } );

        $("#nice_tableBlock").append(deleteProductsButton)
        .append(updateProductButton).append(addProductButton);

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
                retrieveProducts(false);
            },
            log : function(message)
            {

            }
        };
    };
}();

$(document).ready(function()
{
    productManagementWidget = makeProductManagementWidget($("#admin"));
});