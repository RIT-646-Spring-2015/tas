<?php
require 'lib/lib_tas.php';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submitted'] )
{
    /* Security Checkpoint! */
    {
        // Does user exist
        try
        {
            $user = $TAS_DB_MANAGER->loadUserByUsername( $_POST['username'] );
        } catch ( Exception $e )
        {
            $feedback = 'Username does not exist.';
            goto userVerification;
        }
        
        // Is the password correct
        if ( $user->getPassword() != sha1( $_POST['password'] ) )
        {
            $feedback = 'Incorrect password.';
            goto userVerification;
        }
        
        // Is the user account enabled
        if ( !$user->isEnabled() )
        {
            $feedback = 'Your account is disabled. An admin must enable this account.';
            goto userVerification;
        }
        
        // Being session
        session_regenerate_id( true );
        
        $TAS_DB_MANAGER->loggedIn( $user );
        
        // Fingerprint stuff
        // TODO
        $string = SALT;
        
        if ( isset( $_SESSION['HTTP_USER_AGENT'] ) )
        {
            $string .= $_SESSION['HTTP_USER_AGENT'];
        }
    }
    userVerification: // Unfortunately I cannot label a block and break from it =[
}

redirectIfLoggedIn();

?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "TAS | Topic Approval System | Login", array ( "css/formStyle.css" ), array () );
?>

<body>
    <?= templateHeader( false )?>
    <div id="content">
        <?= isset( $feedback )? "<p class='error'><span>$feedback</span></p>":''?>
        <form method="post" action="./login.php?">
            <?= isset($_GET['logout'])? "<p class='message'><span>Logged out successfully!</span></p>":''?>
            <p>
				<label path="username">Username:</label> <input type="text"
					maxlength="16" name="username" placeholder="username"
					<?= (isset($_POST['username']))? 'value="' . $_POST['username'] . '"':''?> />
			</p>
			<p>
				<label path="password">Password:</label> <input type="password"
					name="password" placeholder="password" />
			</p>
			<div id="formButtons">
				<p>
					<input type="submit" value="Login" name="submitted" /> <input
						type="button" value="Sign Up"
						onclick="javascript:window.location='user_management/signup.php'" />
				</p>
			</div>
		</form>
	</div>
</body>

</html>