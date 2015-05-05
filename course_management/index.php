<?php
include './lib/lib_tas.php';

redirectIfLoggedOut();

// This is a secure area!
if ( !$TAS_DB_MANAGER->isAdmin() )
{
    redirectIfLoggedIn();
}
?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Admin", array ( 'css/managementStyle.css' ), 
        array ( 'js/lib/jquery.tablesorter.js', 'js/lib/underscore-min.js',
                        "//code.jquery.com/ui/1.11.3/jquery-ui.min.js",
                        'js/lib/jquery.formatCurrency-1.4.0.min.js',
                        'js/PaginationWidget.js',
                        'js/ProductManagementWidget.js', 'js/FormWidget.js' ) );

?>

<body>

    <?= templateHeader( true, true )?>
    <div id="content">
        <div id="admin"></div>
    </div>
</body>

</html>