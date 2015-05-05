<?php

include_once PROJECT_ROOT . '/oop/CourseDetails.class.php';

final class CourseForm extends CourseDetails
{
    public function __construct( $productId, $name, $description, $price, $quantity, $onSale,
        $sale, $imagePath )
    {
        parent::__construct( $productId, $name, $description, $price, $quantity, $onSale,
        $sale, $imagePath );
    }

    /**
     * Set the product's name.
     */
    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * Set the product's description.
     */
    public function setDescription( $description )
    {
        $this->description = $description;
    }

    /**
     * Set the product's price.
     */
    public function setPrice( $price )
    {
        $this->price = $price;
    }

    /**
     * Set the product's quantity.
     */
    public function setQuantity( $quantity )
    {
        $this->quantity = $quantity;
    }
    
    /**
     * Set whether the product is one sale.
     */
    public function setOnSale( $onSale )
    {
        $this->onSale = $onSale;
    }

    /**
     * Set the product's sale price.
     */
    public function setSalePrice( $sale )
    {
        $this->sale = $sale;
    }

    /**
     * Set the product's image path.
     */
    public function setImagePath( $imagePath )
    {
        $this->imagePath = $imagePath;
    }
}
?>