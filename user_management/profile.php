<?php
include 'lib/lib_project1.php';

redirectIfLoggedOut();

?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( 'Profile', array ( 'css/profileStyle.css' ), array ( 'js/ProfileWidget.js' ) );
?>

<body>
    <?= templateHeader( false, true, true, true )?>
    <div id="content">
        <h3>Well hey there, <?= $_SESSION[USER]->getUsername()?>!</h3>
    </div>
</body>
<span id="user"><?= $_SESSION[USER]->getUsername()?></span>
</html>