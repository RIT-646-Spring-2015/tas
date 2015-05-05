<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$productId = $_POST['productId'];

echo $productId;

$PRODUCT_DB_MANAGER->deleteProduct( $productId );

?>