<?php

abstract class ProductDetails
{

    protected $productId;

    protected $name;

    protected $description;

    protected $price;

    protected $quantity;

    protected $sale;

    protected $onSale;

    protected $imagePath;

    /**
     * Constructs a new product object
     *
     * @param string $productId
     *            the product's unique productId.
     * @param string $name
     *            the product's name.
     * @param string $description
     *            the product's description.
     * @param string $price
     *            the product's price.
     * @param string $quantity
     *            the product's quantity.
     * @param number $sale
     *            the product's sale price.
     * @param number $onSale
     *            is the product on sale.
     * @param number $imagePath
     *            the product's imagePath.
     */
    public function __construct( $productId, $name, $description, $price, $quantity, $onSale, $sale, 
            $imagePath )
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->sale = $sale;
        $this->onSale = $onSale;
        $this->imagePath = $imagePath;
    }

    /**
     * Get the product's productId.
     *
     * @return the product's productId.
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Get the product's name.
     *
     * @return the product's name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the product's description.
     *
     * @return the product's description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the product's price.
     *
     * @return the product's price.
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get the product's quantity.
     *
     * @return the product's quantity.
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Get the product's sale price.
     *
     * @return the product's sale price.
     */
    public function getSalePrice()
    {
        return $this->sale;
    }

    /**
     * Get whether the product is on sale.
     *
     * @return is the product on sale.
     */
    public function isOnSale()
    {
        return $this->onSale;
    }

    /**
     * Get the product's image path.
     *
     * @return the product's image path.
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * Get the product's image path through the site.
     *
     * @return the product's image path through the site.
     */
    public function getImageSitePath()
    {
        return preg_replace( '/^\/home\/([^\/]+)\/Sites/', 'http://kelvin.ist.rit.edu/~$1', 
                $this->imagePath );
    }

    public function toString()
    {
        return sprintf( "%s: %s Price:$%f (Sale Price:$%s) Quantity:<%s>", $this->productId, 
                $this->name, $this->price, $this->sale, $this->quantity );
    }
}

?>