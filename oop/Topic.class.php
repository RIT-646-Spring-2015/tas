<?php
require 'ProductDetails.class.php';

final class Product extends ProductDetails
{

    public static function productFromForm( ProductForm $form )
    {
        return new Member( $form->getProductId(), $form->getName(), $form->getDescription(), 
                $form->getPrice() * 100, $form->getQuantity(), $form->isOnSale(), 
                $form->getSalePrice() * 100, $form->getImagePath() );
    }
}
?>