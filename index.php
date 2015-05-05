<?php
require_once './lib/lib_tas.php';

redirectIfLoggedOut();

if ($TAS_DB_MANAGER->getCurrentUser() == null) {
    $username = '%%';
} else {
    $username = getActingUsername("You cannot shop as another user!");
}
?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead("Topic Approval System", 
        array(
                "css/lib/perfect-scrollbar.min.css"
        ), 
        array(
                "js/lib/underscore-min.js",
                "js/lib/perfect-scrollbar.min.js"
        ));
?>

<body>

    <?= templateHeader( true, true, true, false, true, false, true )?>
    <div id="content">
		<div id="browsingBlock">
			<div id="salesBlock" class="photoslider">
				<h1 id='saleItems'>Sale Items!</h1>
			</div>
			<div id="catalogBlock"></div>
		</div>
		<div id="pagination"></div>
	</div>
</body>
<span id="user"><?= $username ?></span>

</html>
