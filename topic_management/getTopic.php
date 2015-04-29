<?php
require_once '../lib/lib_project1.php';

redirectIfLoggedOut();

$productId = $_POST['productId'];

$user = $PRODUCT_DB_MANAGER->loadProductByProductId( $productId );

    $result['productId'] = $user->getProductId();
    $result['product_name'] = $user->getName();
    $result['price'] = $user->getPrice();
    $result['quantity'] = $user->getQuantity();
    $result['onSale'] = $user->isOnSale()? 'true':'false';
    $result['sale'] = $user->getSalePrice();
    $result['description'] = $user->getDescription();
    $result['imagePath'] = $user->getImagePath();
    $result['imageSitePath'] = $user->getImageSitePath();

    
header('Content-Type: application/json');
echo json_encode( $result );
?>
