<?php

/**
 * Define a custom exception class
 */
class ProductNotFoundException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct( $productName )
    {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct( sprintf( 'Product \'%s\' not found.', $productName ) );
    }

    // custom string representation of object
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>