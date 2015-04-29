<?php
require_once '../lib/lib_project1.php';

redirectIfLoggedOut();

$username = $_POST['username'];
$productId = $_POST['productId'];

$PRODUCT_DB_MANAGER->removeProductFromCart( $username, $productId );

?>