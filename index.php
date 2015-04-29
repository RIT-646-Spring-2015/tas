<?php
require_once './lib/lib_tas.php';

if ( $MEMBER_DB_MANAGER->getCurrentUser() == null )
{
    $username = '%%';
}
else
{
    $username = getActingUsername( "You cannot shop as another user!" );
}
?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "GeneTees!", array ( 'css/paginationStyle.css', "css/catalogStyle.css",
                    "css/lib/photoslider.css\" media=\"screen\"",
                    "css/lib/perfect-scrollbar.min.css" ), 
        array ( "js/lib/photoslider.js", "js/lib/underscore-min.js",
                    "js/lib/perfect-scrollbar.min.js", 'js/lib/jquery.formatCurrency-1.4.0.min.js',
                    "js/PaginationWidget.js",
                    "js/home/CatalogWidget.js", "js/home/SalesWidget.js" ) );
?>

<body>

    <?= templateHeader( true, true, true, false, true, false, true )?>
    <div id="content">
        <div id="browsingBlock">
            <div id="salesBlock" class="photoslider"><h1 id='saleItems'>Sale Items!</h1></div>
            <div id="catalogBlock"></div>
        </div>
        <div id="pagination"></div>
    </div>
</body>
<span id="user"><?= $username ?></span>
<p id="page"><?= isset( $_GET['page'] ) ? $_GET['page'] : 1 ?></p>
<p id="lastPage"><?= ceil( $PRODUCT_DB_MANAGER->getNumberOfProductsNotOnSale() / PRODUCTS_PER_PAGE ) ?></p>

</html>