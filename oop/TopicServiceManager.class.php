<?php
require_once 'PreparedStatementSetter.class.php';
require_once 'exceptions/ProductNotFoundException.class.php';

class ProductServiceManager
{

    /**
     */
    const QUERY_NUMBER_OF_PRODUCTS_SQL = 'SELECT COUNT(ProductId) as count FROM products';

    /**
     */
    const QUERY_NUMBER_OF_PRODUCTS_NOT_ON_SALE_SQL = 'SELECT COUNT(ProductId) as count FROM products WHERE OnSale = 0';

    /**
     */
    const QUERY_PRODUCT_BY_PRODUCT_NAME = 'SELECT ProductID, Name, Description, Price, Quantity, OnSale, Sale, ImagePath FROM products WHERE Name = ?';

    /**
     */
    const QUERY_PRODUCT_BY_PRODUCT_ID = 'SELECT ProductID, Name, Description, Price, Quantity, OnSale, Sale, ImagePath FROM products WHERE ProductID = ?';

    /**
     */
    const QUERY_CART_BY_USERNAME = 'SELECT user_cart.username, products.ProductId, user_cart.quantity, Name, Description, Price, products.Quantity, OnSale, Sale, ImagePath FROM user_cart LEFT JOIN products ON user_cart.ProductId=products.ProductId WHERE user_cart.username = ?';

    /**
     */
    const QUERY_CART_BY_USERNAME_AND_PRODUCT_ID = 'SELECT user_cart.username, products.ProductId, user_cart.quantity, Name, Description, Price, products.Quantity, OnSale, Sale, ImagePath FROM user_cart LEFT JOIN products ON user_cart.ProductId=products.ProductId WHERE user_cart.username = ? AND user_cart.ProductId = ?';

    /**
     */
    const SELECT_ALL_PRODUCTS_SQL = 'SELECT ProductID, Name, Description, Price, Quantity, OnSale, Sale, ImagePath FROM products';

    /**
     */
    const SELECT_ALL_PRODUCTS_ON_SALE_SQL = 'SELECT ProductID, Name, Description, Price, Quantity, OnSale, Sale, ImagePath FROM products WHERE OnSale = 1';

    /**
     */
    const SELECT_ALL_CARTS_SQL = 'SELECT users.username, products.ProductId, Name, Description, Price, Quantity, OnSale, Sale, ImagePath FROM users RIGHT JOIN user_cart ON users.username=user_cart.username LEFT JOIN products ON user_cart.ProductId=products.ProductId';

    /**
     */
    const NEW_PRODUCT_SQL = 'INSERT INTO products (Name, Description, Price, Quantity, OnSale, Sale, ImagePath) VALUES ( ?, ?, ?, ?, ?, ?, ? )';

    /**
     */
    const NEW_USER_CART_ENTRY_SQL = 'INSERT INTO user_cart (username, ProductId, quantity) VALUES( ?, ?, ? )';

    /**
     */
    const LAST_INSERT_ID_SQL = 'SELECT LAST_INSERT_ID()';

    /**
     */
    const DELETE_PRODUCT_SQL = 'DELETE FROM products WHERE ProductId = ?';

    /**
     */
    const DELETE_USER_CART_SQL = 'DELETE FROM user_cart WHERE username = ?';

    /**
     */
    const DELETE_PRODUCT_FROM_USER_CART_SQL = 'DELETE FROM user_cart WHERE username = ? AND ProductId = ?';

    /**
     */
    const UPDATE_PRODUCT_SQL = 'UPDATE products SET Name = ?, Description = ?, Price = ?, Quantity = ?, OnSale = ?, Sale = ?, ImagePath = ? WHERE ProductId = ?';

    /**
     */
    const UPDATE_PRODUCT_QUANTITY_SQL = 'UPDATE products SET Quantity = ? WHERE ProductId = ?';

    /**
     */
    const UPDATE_CART_PRODUCT_QUANTITY_SQL = 'UPDATE user_cart SET Quantity = ? WHERE username = ? AND ProductId = ?';

    /**
     * This is a Singleton architecture I am trying to acheive.
     */
    public static function &getInstance()
    {
        static $instance = null;
        if ( null === $instance )
        {
            $instance = new static();
        }
        
        return $instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    private function getDB()
    {
        try
        {
            $db = new SQLite3( MEMBER_DB );
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
        return $db;
    }

/* * * * * * * * * * * *\
 *
 * PRODUCT MANAGEMENT
 *
\* * * * * * * * * * * */

    public function createProduct( ProductForm $form )
    {
        $stmt = $this->getDB()->prepare( self::NEW_PRODUCT_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($form )
                {
                    $ps->bindValue( 1, $form->getName(), SQLITE3_TEXT );
                    $ps->bindValue( 2, $form->getDescription(), SQLITE3_TEXT );
                    $ps->bindValue( 3, $form->getPrice(), SQLITE3_INTEGER );
                    $ps->bindValue( 4, $form->getQuantity(), SQLITE3_INTEGER );
                    $ps->bindValue( 5, $form->isOnSale(), SQLITE3_INTEGER );
                    $ps->bindValue( 6, $form->getSalePrice(), SQLITE3_INTEGER );
                    $ps->bindValue( 7, $form->getImagePath(), SQLITE3_TEXT );
                }, $stmt );
    }

    public function updateProduct( ProductDetails $product, $tmp_image = '' )
    {
        global $MEMBER_DB_MANAGER;

        $MEMBER_DB_MANAGER->failIfNotAdmin();
        
        $oldProduct = $this->loadProductByProductId( $product->getProductId() );
        
        $stmt = $this->getDB()->prepare( self::UPDATE_PRODUCT_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($product )
                {
                    $ps->bindValue( 1, $product->getName(), SQLITE3_TEXT );
                    $ps->bindValue( 2, $product->getDescription(), SQLITE3_TEXT );
                    $ps->bindValue( 3, $product->getPrice(), SQLITE3_INTEGER );
                    $ps->bindValue( 4, $product->getQuantity(), SQLITE3_INTEGER );
                    $ps->bindValue( 5, $product->isOnSale(), SQLITE3_INTEGER );
                    $ps->bindValue( 6, $product->getSalePrice(), SQLITE3_INTEGER );
                    $ps->bindValue( 7, $product->getImagePath(), SQLITE3_TEXT );
                    $ps->bindValue( 8, $product->getProductID(), SQLITE3_INTEGER );
                }, $stmt );
        
        if ( !empty($tmp_image) && !unlink( $oldProduct->getImagePath() ) )
            echo '';
        
        if ( !empty($tmp_image) && !move_uploaded_file( $tmp_image, $product->getImagePath() ) )
            throw new Exception( 'File could not be uploaded' );
    }

    public function deleteProduct( $productId )
    {
        global $MEMBER_DB_MANAGER;

        $product = $this->loadProductByProductId( $productId );
        
        try
        {
            $MEMBER_DB_MANAGER->failIfNotAdmin();
            
            $stmt = $this->getDB()->prepare( self::DELETE_PRODUCT_SQL );
            $stmt->bindParam( 1, $productId, SQLITE3_INTEGER );
            
            $stmt->execute();
            
            if ( !unlink( $product->getImagePath() ) )
            {
                throw new Exception( 'Could not delete prodcut image file' );
            }
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

/* * * * * * * * * * * *\
 *
 * CART MANAGEMENT
 *
\* * * * * * * * * * * */

    public function insertProductIntoUserCart( $username, $productId )
    {
        global $MEMBER_DB_MANAGER;

        if ( $username != $MEMBER_DB_MANAGER->getCurrentUser()->getUsername() )
        {
            $MEMBER_DB_MANAGER->failIfNotAdmin();
        }

        // Get Amount Left in Inventory
        $product = $this->loadProductByProductId( $productId );

        $inventoryQuantity = $product->getQuantity();

        if ( $inventoryQuantity <= 0 )
        {
            return array( 'error' => sprintf( 'Product Id: \'%d\' is out of stock', $productId ) );
        }

        // Get Previous Cart Quantity
        $stmt = $this->getDB()->prepare( self::QUERY_CART_BY_USERNAME_AND_PRODUCT_ID );
        $result = PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $username, $productId )
                {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );

        $cartQuantity = $result? $result['quantity'] : 0;

        // Take from the inventory, and give to the cart
        $inventoryQuantity --;
        $cartQuantity ++;

        // Update inventory
        $stmt = $this->getDB()->prepare( self::UPDATE_PRODUCT_QUANTITY_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $inventoryQuantity, $productId )
                {
                    $ps->bindValue( 1, $inventoryQuantity, SQLITE3_INTEGER );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );

        // Update cart
        if ( $result )
        {
            $stmt = $this->getDB()->prepare( self::UPDATE_CART_PRODUCT_QUANTITY_SQL );
            PreparedStatementSetter::setValuesAndExecute( 
                    function ( SQLite3Stmt &$ps ) use ( $username, $productId, $cartQuantity )
                    {
                        $ps->bindValue( 1, $cartQuantity, SQLITE3_INTEGER );
                        $ps->bindValue( 2, $username, SQLITE3_TEXT );
                        $ps->bindValue( 3, $productId, SQLITE3_INTEGER );
                    }, $stmt );
        } else
        {
            $stmt = $this->getDB()->prepare( self::NEW_USER_CART_ENTRY_SQL );
            PreparedStatementSetter::setValuesAndExecute( 
                    function ( SQLite3Stmt &$ps ) use ( $username, $productId, $cartQuantity )
                    {
                        $ps->bindValue( 1, $username, SQLITE3_TEXT );
                        $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                        $ps->bindValue( 3, $cartQuantity, SQLITE3_INTEGER );
                    }, $stmt );
        }

        return array( 'productId' => $productId, 'quantity' => $inventoryQuantity );
    }

    public function updateProductInCart( $username, $productId, $newQuantity )
    {
        global $MEMBER_DB_MANAGER;

        if ( $username !=$MEMBER_DB_MANAGER->getCurrentUser()->getUsername() )
        {
            $MEMBER_DB_MANAGER->failIfNotAdmin();
        }

        if ( $newQuantity < 0 )
            return array( 'error' =>
                'Sorry, but we will not take our own products from you. (Negative Quantity and all...)' );

        if ( $newQuantity == 0 )
        {
            $this->removeProductFromCart( $username, $productId);
            return;
        }

        // Get Amount in Inventory
        $product = $this->loadProductByProductId( $productId );

        $inventoryQuantity = $product->getQuantity();

        // Get Current Cart Quantity
        $stmt = $this->getDB()->prepare( self::QUERY_CART_BY_USERNAME_AND_PRODUCT_ID );
        $result = PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $username, $productId )
                {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );

        $cartQuantity = $result? $result['quantity'] : 0;

        // Add it back for now
        $inventoryQuantity += $cartQuantity;

        // We aren't getting jipped today!
        if ( $inventoryQuantity < $newQuantity )
            return array( 'error' => 'Sorry, but there is not enough of that item.' );

        // Take from inventory
        $inventoryQuantity -= $newQuantity;

        // Update inventory
        $stmt = $this->getDB()->prepare( self::UPDATE_PRODUCT_QUANTITY_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $inventoryQuantity, $productId )
                {
                    $ps->bindValue( 1, $inventoryQuantity, SQLITE3_INTEGER );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );
        
        // Update User's Cart
        $stmt = $this->getDB()->prepare( self::UPDATE_CART_PRODUCT_QUANTITY_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $username, $productId, $newQuantity )
                {
                    $ps->bindValue( 1, $newQuantity, SQLITE3_INTEGER );
                    $ps->bindValue( 2, $username, SQLITE3_TEXT );
                    $ps->bindValue( 3, $productId, SQLITE3_INTEGER );
                }, $stmt );
    }

    public function removeProductFromCart( $username, $productId )
    {
        global $MEMBER_DB_MANAGER;

        $user = $MEMBER_DB_MANAGER->getCurrentUser();

        if ( $user->getUsername() != $username )
        {
            $MEMBER_DB_MANAGER->failIfNotAdmin();
        }

        // Get the quantity of that product in the cart for the user
        $stmt = $this->getDB()->prepare( self::QUERY_CART_BY_USERNAME_AND_PRODUCT_ID );
        $result = PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $username, $productId )
                {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );

        $cartQuantity = $result? $result['quantity'] : 0;

        // If the quantity is 0, we're done here
        if ( $cartQuantity <= 0 ) return;

        // If the quantity is greater than 0,
        //   add the quantity back to the inventory
        //   & delete the user_cart entry.

        // Get Amount in Inventory
        $product = $this->loadProductByProductId( $productId );

        $inventoryQuantity = $product->getQuantity();

        $inventoryQuantity += $cartQuantity;

        // Update inventory
        $stmt = $this->getDB()->prepare( self::UPDATE_PRODUCT_QUANTITY_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $inventoryQuantity, $productId )
                {
                    $ps->bindValue( 1, $inventoryQuantity, SQLITE3_INTEGER );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );

        // Delete Product from User's Cart
        $stmt = $this->getDB()->prepare( self::DELETE_PRODUCT_FROM_USER_CART_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $username, $productId )
                {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                    $ps->bindValue( 2, $productId, SQLITE3_INTEGER );
                }, $stmt )->fetchArray( SQLITE3_ASSOC );
    }

    /**
     * THIS IS WHY PHP NEEDS TO HAVE A NAMESPACE VISIBILITY MODIFIER!
     * For this simple assignment however..
     * no harm done necessarily.
     */
    public function deleteUserCart( $username )
    {
        global $MEMBER_DB_MANAGER;

        if ( $MEMBER_DB_MANAGER->getCurrentUser()->getUsername() != $username() )
        {
            $MEMBER_DB_MANAGER->failIfNotAdmin();
        }

        // Put all their stuff back
        $products = $this->getCartByUsername( $username );

        foreach( $products as $product )
        {
            $this->removeProductFromCart( $username, $product->getProductID() );
        }

        $stmt = $this->getDB()->prepare( self::DELETE_USER_CART_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($username )
                {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                }, $stmt );
    }

    /**
     * Get a User's cart given a username
     */
    public function getCartByUsername( $username )
    {
        global $MEMBER_DB_MANAGER;

        if ( $MEMBER_DB_MANAGER->getCurrentUser()->getUsername() != $username )
        {
            $MEMBER_DB_MANAGER->failIfNotAdmin();
        }
        
        $stmt = $this->getDB()->prepare( self::QUERY_CART_BY_USERNAME );
        $results = PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($username )
                {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                }, $stmt );
        
        return $this->extractData( $results, array( 'ProductServiceManager', 'mapCartRow' ) );
    }

/* * * * * * * * * * * *\
 *
 * LOAD PRODUCTS
 *
\* * * * * * * * * * * */

    public function getProducts()
    {
        return $this->extractData( $this->getDB()->query( self::SELECT_ALL_PRODUCTS_SQL ), array( 'ProductServiceManager', 'mapRow' ) );
    }

    public function getProductsOnSale()
    {
        return $this->extractData( $this->getDB()->query( self::SELECT_ALL_PRODUCTS_ON_SALE_SQL ), array( 'ProductServiceManager', 'mapRow' ) );
    }

    public function getNumberOfProducts()
    {
         $result = $this->getDB()->query( self::QUERY_NUMBER_OF_PRODUCTS_SQL );

         $count = $result->fetchArray( SQLITE3_ASSOC );
         return $count['count'];
    }

    public function getNumberOfProductsNotOnSale()
    {
         $result = $this->getDB()->query( self::QUERY_NUMBER_OF_PRODUCTS_NOT_ON_SALE_SQL );

         $count = $result->fetchArray( SQLITE3_ASSOC );
         return $count['count'];
    }

/* * * * * * * * * * * *\
 *
 * LOAD PRODUCTS BY
 *   ID OR BY NAME
 *
\* * * * * * * * * * * */

    /**
     * Executes the SQL <tt>QUERY_PRODUCT_BY_PRODUCT_ID</tt> and returns a list of
     * Product objects.
     * There should normally only be one matching product.
     */
    public function loadProductByProductId( $productId )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_PRODUCT_BY_PRODUCT_ID );
        $stmt->bindParam( 1, $productId, SQLITE3_INTEGER );
        
        $result = $stmt->execute();
        
        $products = self::extractData( $result, array( 'ProductServiceManager', 'mapRow' ) );
        
        if ( count( $products ) == 0 )
        {
            throw new ProductNotFoundException( 'Id:' . $productId );
        }
        
        return $products[0];
    }

    /**
     * Executes the SQL <tt>QUERY_PRODUCT_BY_PRODUCT_NAME</tt> and returns a list of
     * Product objects.
     * There should normally only be one matching product.
     */
    public function loadProductByProductName( $productName )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_PRODUCT_BY_PRODUCT_NAME );
        $stmt->bindParam( 1, $productName, SQLITE3_TEXT );
        
        $result = $stmt->execute();

        $products = self::extractData( $result, array( 'ProductServiceManager', 'mapRow' ) );
        
        if ( count( $products ) == 0 )
        {
            throw new ProductNotFoundException( $productName );
        }
        
        return $products[0];
    }

/* * * * * * * * * * * *\
 *
 * DATA EXTRACTION
 *
\* * * * * * * * * * * */

    private static function mapRow( $rs )
    {
        $product = new Product( $rs['ProductId'], $rs['Name'], $rs['Description'], 
                intval( $rs['Price'] ), intval( $rs['Quantity'] ), $rs['OnSale'], 
                intval( $rs['Sale'] ), $rs['ImagePath'] );
        
        return $product;
    }

    private static function mapCartRow( $rs )
    {
        $product = self::mapRow( $rs );

        $productInCart = array( 'ProductId' => $rs['ProductId'], 'Name' => $rs['Name'], 'Description' => $rs['Description'], 
                'Price' => intval( $rs['Price'] ), 'CartQuantity' => intval( $rs['quantity'] ), 'OnSale' => $rs['OnSale'],
                'inventoryQuantity' => intval( $rs['Quantity'] ),
                'Sale' => intval( $rs['Sale'] ), 'ImagePath' => $rs['ImagePath'], 'ImageSitePath' => $product->getImageSitePath() );
        
        return $productInCart;
    }

    private static function extractData( $rs, $rowMapper )
    {
        $results = array ();
        
        while ( $res = $rs->fetchArray( SQLITE3_ASSOC ) )
        {
            if ( !isset( $res['ProductId'] ) )
                continue;
            
            $results[] = call_user_func( $rowMapper, $res );
        }
        
        return $results;
    }
}
?>
