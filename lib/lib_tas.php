<?php
// Member Library
require_once PROJECT_ROOT . '/oop/Member.class.php';
require_once PROJECT_ROOT . '/oop/MemberForm.class.php';
require_once PROJECT_ROOT . '/oop/SignUpFormValidator.class.php';
require_once PROJECT_ROOT . '/oop/MemberServiceManager.class.php';

// Product Library
require_once PROJECT_ROOT . '/oop/Product.class.php';
require_once PROJECT_ROOT . '/oop/ProductForm.class.php';
require_once PROJECT_ROOT . '/oop/ProductFormValidator.class.php';
require_once PROJECT_ROOT . '/oop/ProductServiceManager.class.php';

// I can move this to the .htaccess file in ~/Sites/756/project1 if I want to
//   php_flag session.auto_start on
session_start();

/*
 * * * * * * * * * * *
 * Load in properties
 * * * * * * * * * * */
$props = parse_ini_file( PROJECT_ROOT . '/resources/project1.properties', true );

/*
 * * * * * * * * * *
 * Define Constants
 * * * * * * * * * */
// TODO: Installation bash script?
define( 'PROJECT_ROOT', $props['tas']['BASE_FOLDER'] );
define( 'SITE_ROOT', $props['tas']['SITE_CONTEXT'] );
define( 'PROFILE_LOC', SITE_ROOT . '/user_management/profile.php' );

define( 'USER', $props['session-values']['USER'] );
define( 'SALT', $props['other']['SALT'] );
// define( 'PRODUCTS_PER_PAGE', $props['other']['PRODUCTS_PER_PAGE'] );

// define( 'PRODUCT_IMAGE_DIR', $props['product-database']['imagedb'] );
// define( 'MIN_SALE_ITEMS', $props['product-database']['MIN_SALE_ITEMS'] );
// define( 'MAX_SALE_ITEMS', $props['product-database']['MAX_SALE_ITEMS'] );

define( 'TAS_DB', $props['tas-database']['dbname'] );
define( 'NEW_USER_ENABLED', $props['tas-database']['NEW_USER_ENABLED'] );
$TAS_DB_MANAGER = MemberServiceManager::getInstance();
define( 'USERMANAGEMENT_OPTION', '<a id="userManagementOption" href="' . SITE_ROOT . '/user_management">Manage Users</a>' );
define( 'TOPICMANAGEMENT_OPTION', '<a id="topicManagementOption" href="' . SITE_ROOT . '/topic_management.php">Manage Topics</a>' );
define( 'COURSEMANAGEMENT_OPTION', '<a id="courseManagementOption" href="' . SITE_ROOT . '/course_management.php">Manage Courses</a>' );
define( 'LOGIN_OPTION', '<a id="loginOption" href="' . SITE_ROOT . '/login.php">Login</a>' );
define( 'LOGOUT_OPTION', '<a id="logoutOption" href="' . SITE_ROOT . '/user_management/logout.php">Logout</a>' );
// define( 'VIEW_CART_OPTION', '<a id="viewCartOption" href="' . SITE_ROOT . '/cart.php">View Cart</a>' );
// define( 'CONTINUE_SHOPPING_OPTION', '<a id="continueShoppingOption" href="' . SITE_ROOT . '/">Continue Shopping</a>' );


/*
 * * * * * * * * * *
 * Helper Functions
 * * * * * * * * * */

/**
 * Redirect user if they are logged in.
 *
 * @param string $location
 *            the location to redirect to. By default, this is <code>./</code>
 */
function redirectIfLoggedIn( $location = PROFILE_LOC )
{
    if ( isset( $_SESSION[USER] ) )
    {
        header( "Location: $location" );
        die();
    }
}

/**
 * Redirect user to login page if they are not yet logged in.
 */
function redirectIfLoggedOut( $params = '' )
{
    if ( !isset( $_SESSION[USER] ) )
    {
        header( 'Location: ' . SITE_ROOT . '/user_management/login.php' . $params );
        die();
    }
}

/**
 *
 */
function getActingUsername( $messageOnFail )
{
    global $TAS_DB_MANAGER;

    if ( isset( $_GET['username'] ) )
    {
        $username = $_GET['username'];

        // Secure Area!
        if ( $TAS_DB_MANAGER->getCurrentUser()->getUsername() != $username )
        {
            try {
                $TAS_DB_MANAGER->failIfNotAdmin( $messageOnFail );
            } catch ( InadequateRightsException $e )
            {
                die( $e->getMessage() );
            }
        }
    }
    else
    {
        $username = $TAS_DB_MANAGER->getCurrentUser()->getUsername();
    }

    return $username;
}

/**
 * Generic preparation of form input
 *
 * @param string $data
 *            potentially 'dirty' string.
 * @return string the same string but trimmed, stripped, and html happy
 */
function clean_input( $data, $currency = false )
{
    $data = trim( $data );
    $data = stripslashes( $data );
    $data = htmlspecialchars( $data );

    if ( $currency )
    {
        $data = preg_replace( '/[\$\.]/', "", $data );
    }
    
    return $data;
}

/*
 * * * * * * * * *
 * Page Templates
 * * * * * * * * */

/**
 * Head Template
 *
 * @param string $title
 *            the title of the page
 * @param array $styles
 *            the paths to any styles to add
 * @param array $scripts
 *            the paths to any javascript scripts to add
 * @return string the template head tag element
 */
function templateHead( $title = "Page", $styles, $scripts )
{
    // head tag beginning
    $head = '
<head>
<meta charset="UTF-8">

<title>Project 1 | '.$title.'</title>
<meta name="description" content="Project 1 | E-Commerce site">
<meta name="author" content="Alex Aiezza">

<link rel="stylesheet" type="text/css" href="' . SITE_ROOT . '/css/mainStyle.css">
<link rel="stylesheet" type="text/css" href="' . SITE_ROOT . '/css/lib/perfect-scrollbar.min.css">
';
    
    // Extra styles
    foreach ( $styles as $style )
    {
        $head .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$style\">\n";
    }
    
    $head .= '

<link rel="icon" type="image/ico" href="' . SITE_ROOT . '/images/favicon.ico">

<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="' . SITE_ROOT . '/js/HeaderWidget.js"></script>
<script src="' . SITE_ROOT . '/js/lib/perfect-scrollbar.min.js"></script>

';
    
    // Extra javascripts
    foreach ( $scripts as $script )
    {
        $head .= "<script src='$script'></script>\n";
    }
    
    $head .= "\n<h2 id='title'>$title</h2>\n\n";
    $head .= "</head>\n";
    
    return $head;
}

/**
 * Header template
 *
 * @param bool $linkProfile
 *            if true, will add the linkProfile class to the header div. Later on, the
 *            HeaderWidget javascript will cause the companyLogo to become
 *            a link back to the user's profile page.
 * @param bool $logoutOption
 *            if true, will add the option in the header to logout
 * @param bool $userManagementOption
 *            if true, will add the option in the header to manage users.
 *            NOTE: even if set to true, if the current user is not an
 *            administrator, the option will not be presented.
 * @return string template header for the e-commerce site
 */
function templateHeader(
    $linkProfile = false,
    $logoutOption = false,
    $productManagementOption = false,
    $userManagementOption = false,
    $viewCartOption = false,
    $continueShoppingOption = false,
    $loginIfLoggedOut = false )
{
    global $MEMBER_DB_MANAGER;

    $header = "<div id=\"header\"";
    $header .= $linkProfile ? "class=\"linkProfile\"" : "";
    $header .= "></div>";

    if ( $MEMBER_DB_MANAGER->getCurrentUser() == null )
    {
        $linkProfile = $logoutOption = $productManagementOption =
        $userManagementOption = $viewCartOption =
        $continueShoppingOption = false;
    }
    else $loginIfLoggedOut = false;

    if ( $loginIfLoggedOut )
        $header .= LOGIN_OPTION;
        
    if ( $logoutOption )
        $header .= LOGOUT_OPTION;

    if ( $userManagementOption && $MEMBER_DB_MANAGER->isAdmin() )
        $header .= USERMANAGEMENT_OPTION;

    if ( $productManagementOption && $MEMBER_DB_MANAGER->isAdmin() )
        $header .= PRODUCTMANAGEMENT_OPTION;

    if ( $viewCartOption )
        $header .= VIEW_CART_OPTION;

    if ( $continueShoppingOption )
        $header .= CONTINUE_SHOPPING_OPTION;
    
    return $header;
}

?>
