<?php
require_once './lib/lib_tas.php';

redirectIfLoggedOut();

?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Topic Approval System", 
        array ( "css/lib/perfect-scrollbar.min.css" ), 
        array ( "js/lib/underscore-min.js", "js/lib/perfect-scrollbar.min.js" ) );
?>

<body>

    <?= templateHeader( true, true, true, false, true, false, true )?>
    <div id="content"></div>
</body>
<span id="user"><?= $username ?></span>

</html>
