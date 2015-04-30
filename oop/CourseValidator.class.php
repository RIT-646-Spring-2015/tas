<?php

class ProductFormValidator
{

    const ACCEPTABLE_PRODUCT_NAME = '/[^\w\p{P} ]{3,50}$/';

    /**
     * Validates a new product
     *
     * @param ProductForm $product            
     * @return array
     */
    public static function validateRequiredFields( ProductForm $product )
    {
        $errors = array ();
        self::validates( $errors, $product->getName(), 'Name required.' );
        self::validates( $errors, $product->getDescription(), 'Description required.' );
        self::validates( $errors, $product->getPrice(), 'Price required.' );
        self::validates( $errors, $product->getQuantity(), 'Quantity required.' );
        self::validates( $errors, $product->getSalePrice(), 'Sale Price required.' );
        self::validates( $errors, $product->getImagePath(), 'Image required.' );
        
        return $errors;
    }

    public static function validate( ProductForm $product, $newProduct = true )
    {
        global $PRODUCT_DB_MANAGER;

        $errors = array ();

        // Validate Product Name
        if ( !preg_match( '/^[\w\p{P}\s:]{3,50}$/', $product->getName() ) )
        {
            $errors[] .= 'Name must only be 3 to 50 characters [a-zA-Z0-9_ \':]';
        }

        // Validate Product Description
        if ( !preg_match( '/^[\w\p{P}\s:]{1,200}$/', $product->getDescription() ) )
        {
            $errors[] .= 'Description must be less than 200 characters [a-zA-Z0-9_ \':]';
        }
        
        // Validate Quantity
        if ( !is_integer( $product->getQuantity() ) || $product->getQuantity() < 0 )
        {
            $errors[] .= 'Quantity must be an amount greater than or equal to 0';
        }
        
        // Validate Price
        if ( !is_numeric( $product->getPrice() ) || $product->getPrice() < 0 )
        {
            $errors[] .= 'Price must be an amount greater than or equal to 0';
        }
        
        // Validate Sale Price
        if ( !is_numeric( $product->getSalePrice() ) || $product->getSalePrice() < 0 )
        {
            $errors[] .= 'Sale Price must be an amount greater than or equal to 0';
        }

        $productsOnSale = count( $PRODUCT_DB_MANAGER->getProductsOnSale() );

        // This obnoxious if-if-if block is unfortunately necessary for
        //  determining an error in the number of sale items in the catalog
        if ( $newProduct )
        {
            if ( $product->isOnSale() )
            {
                if ( $productsOnSale >= MAX_SALE_ITEMS )
                {
                    $errors[] .= sprintf( 'A maximum of %d items may be on sale. So this item can NOT be on sale!', MAX_SALE_ITEMS );
                }

            } else
            {
                if ( $productsOnSale < MIN_SALE_ITEMS )
                {
                    $errors[] .= sprintf( 'A minimum of %d items must be on sale. So this item must be on sale!', MIN_SALE_ITEMS );
                }
            }
        } else
        {
            $productWasOnSale = $PRODUCT_DB_MANAGER->loadProductByProductId( $product->getProductID() )->isOnSale();

            if ( $product->isOnSale() )
            {
                if ( !$productWasOnSale )
                {
                    if ( $productsOnSale >= MAX_SALE_ITEMS )
                    {
                        $errors[] .= sprintf( 'A maximum of %d items may be on sale. So this item can NOT be on sale!', MAX_SALE_ITEMS );
                    }
                }

            } else
            {
                if ( $productWasOnSale )
                {
                    if ( $productsOnSale <= MIN_SALE_ITEMS )
                    {
                        $errors[] .= sprintf( 'A minimum of %d items must be on sale. So this item must be on sale!', MIN_SALE_ITEMS );
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     *
     * @param array $errors            
     * @param string $field            
     * @param string $errorStatement            
     */
    static function validates( &$errors, $field, $errorStatement )
    {
        if ( empty( $field ) && !is_numeric( $field ) )
        {
            $errors[] .= $errorStatement;
        }
    }
}

?>
