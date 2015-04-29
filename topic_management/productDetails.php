<?php
require_once '../lib/lib_project1.php';

redirectIfLoggedOut();

$productId = $_GET['productId'];

// See if product exists
try
{
    $PRODUCT_DB_MANAGER->loadProductByProductId( $productId );
} catch ( ProductNotFoundException $e )
{
    echo $e->getMessage();
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $errors = array ();

    // Clean Data
    $_POST['product_name'] = clean_input( $_POST['product_name'] );
    $_POST['price'] = intval( clean_input( $_POST['price'], true ) );
    $_POST['quantity'] = intval( clean_input( $_POST['quantity'] ) );
    $_POST['onSale'] = isset( $_POST['onSale'] ) ? true : false;
    $_POST['sale'] = intval( clean_input( $_POST['sale'], true ) );
    $_POST['description'] = clean_input( $_POST['description'] );

    // Product Image
    $image_file = !empty( $_FILES['imagePath']['name'] ) ? PRODUCT_IMAGE_DIR .
             basename( $_FILES['imagePath']['name'] ) : $PRODUCT_DB_MANAGER->loadProductByProductId( 
                    $productId )->getImagePath();

    $product = new ProductForm( $productId, $_POST['product_name'], $_POST['description'], 
            $_POST['price'], $_POST['quantity'], $_POST['onSale'], $_POST['sale'], $image_file );

    $errors = array ();
    $errors = array_merge( ProductFormValidator::validateRequiredFields( $product ),
        ProductFormValidator::validate( $product, false ) );
    
    if ( !empty( $_FILES['imagePath']["tmp_name"] ) &&
             getimagesize( $_FILES['imagePath']["tmp_name"] ) === false )
    {
        $errors[] .= 'File is not an image';
    }

    if ( count( $errors ) == 0 )
    {
        // SUBMIT A CHANGE
        $PRODUCT_DB_MANAGER->updateProduct( $product, $_FILES['imagePath']['tmp_name'] );
        
        header( 'Location: ./productDetails.php?' . $_SERVER['QUERY_STRING'] );
        die();
    }

    foreach ( $errors as $error )
    {
        $message .= "<p>$error</p>";
    }
}

?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Product $productId Details", 
        array ( '../css/formStyle.css', '../css/detailsStyle.css' ), 
        array ( '../js/lib/jquery.tablesorter.js', '../js/lib/underscore-min.js', 
                        '../js/lib/jquery.formatCurrency-1.4.0.min.js', '../js/FormWidget.js', 
                        '../js/ProductDetailsWidget.js' ) );
?>

<body>
    <?= templateHeader(true, true, true)?>
    <div id="content">
        <form method="POST" enctype="multipart/form-data">
            <div id="nice_tableBlock">
                <table id="detailsTable">
                    <tbody>
                        <tr id="product_nameRow">
                            <td><label for="product_name">Product Name</label></td>
                            <td><input type="text" id="product_name" name="product_name" /></td>
                        </tr>
                        <tr id="priceRow">
                            <td><label for="price">Retail Price</label></td>
                            <td><input type="text" id="price" class='currency' name="price" /></td>
                        </tr>
                        <tr id="quantityRow">
                            <td><label for="quantity">Quantity</label></td>
                            <td><input type="number" id="quantity" name="quantity" /></td>
                        </tr>
                        <tr id="onSaleRow">
                            <td><label for="onSale">On Sale</label></td>
                            <td><input type="checkbox" id="onSale" name="onSale" /></td>
                        </tr>
                        <tr id="saleRow">
                            <td><label for="sale">Sale Price</label></td>
                            <td><input type="text" id="sale" class='currency' name="sale" /></td>
                        </tr>
                        <tr id="descriptionRow">
                            <td><label for="description">Description</label></td>
                            <td><textarea type="text" id="description" name="description"></textarea></td>
                        </tr>
                        <tr id="imagePathRow">
                            <td><label for="imagePath">Product Image</label></td>
                            <td><input type="file" accept='image/*' id="imagePath" name="imagePath" /></td>
                        </tr>
                        <?php
                        if ( isset( $message ) )
                        {
                            echo "
                            <tr>
                                <td colspan='2' style='color: red'><p>$message</p></td>
                            </tr>";
                        }
                        ?>
                        <tr id="buttonsRow" class="permanent">
                            <td></td>
                            <td><input type="submit" id="updateFieldsButton" name="submitted"
                                    value="Update With New Info"
                                ></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <span id="productId"><?= $productId ?></span>

</body>

</html>
