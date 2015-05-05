<?php
require_once '../lib/lib_tas.php';

header('Content-Type: application/json');

$username = $_POST['username'];
$productId = $_POST['productId'];
$newQuantity = $_POST['newQuantity'];

echo json_encode( $PRODUCT_DB_MANAGER->updateProductInCart( $username, $productId, $newQuantity ) );
?>
 