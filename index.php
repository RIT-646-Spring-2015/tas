<?php
require_once './lib/lib_tas.php';

redirectIfLoggedOut();
redirectIfLoggedIn();

?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Topic Approval System" );
?>

<body>

    <?= templateHeader( true, true, true, false, true, false, true )?>
    <div id="content">
		<h3>How did you get here?</h3>
	</div>
</body>
<span id="user"><?= $username ?></span>

</html>
