<?php
require_once '../lib/lib_tas.php';

$result = array();

$page = isset( $_POST['page'] )? intval( $_POST['page'] ) : false;
$notOnSale = isset( $_POST['notOnSale'] )? intval( $_POST['notOnSale'] ) : false;

$i = 0;
foreach ( $PRODUCT_DB_MANAGER->getProducts() as $product )
{

    if ( $page )
    {
        $inRange = $i >= (($page*PRODUCTS_PER_PAGE)-PRODUCTS_PER_PAGE) &&
                $i < ($page*PRODUCTS_PER_PAGE);

        // $debug[] = array( 'page' => $page, 'i' => $i, 'i >=' => (($page*PRODUCTS_PER_PAGE)-PRODUCTS_PER_PAGE),
        //         'i <' => ($page*PRODUCTS_PER_PAGE), 'inRange' => $inRange, 'product on sale' =>  $product->isOnSale() );

        if ( $notOnSale !== false )
        {
            if ( $notOnSale > 0 )
            {
                if ( !$product->isOnSale() && !$inRange )
                    $i++;
                if ( $product->isOnSale() || !$inRange )
                    continue;
            } else
            {
                if ( $product->isOnSale() && !$inRange )
                    $i++;
                if ( !$product->isOnSale() || !$inRange )
                    continue;
            }
        }
        else
        {
            if ( !$inRange ) continue;
                
        }
    }

    $result[$i]['ProductId'] = $product->getProductId();
    $result[$i]['Name'] = $product->getName();
    $result[$i]['Description'] = $product->getDescription();
    $result[$i]['Price'] = $product->getPrice();
    $result[$i]['Quantity'] = $product->getQuantity();
    $result[$i]['Sale'] = $product->getSalePrice();
    $result[$i]['OnSale'] = $product->isOnSale();
    $result[$i]['ImagePath'] = $product->getImagePath();
    $result[$i++]['ImageSitePath'] = $product->getImageSitePath();
}

header('Content-Type: application/json');
echo json_encode( $result );
// echo json_encode( $debug );

?>