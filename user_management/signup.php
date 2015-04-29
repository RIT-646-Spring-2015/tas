<?php
require 'lib/lib_project1.php';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submitted'] )
{
    // Clean Data
    $_POST['first_name'] = clean_input( $_POST['first_name'] );
    $_POST['last_name'] = clean_input( $_POST['last_name'] );
    $_POST['email'] = clean_input( $_POST['email'] );
    $_POST['username'] = clean_input( $_POST['username'] );
    $_POST['password'] = clean_input( $_POST['password'] );
    $_POST['confirmPassword'] = clean_input( $_POST['confirmPassword'] );
    
    $user = new MemberForm( $_POST['username'], $_POST['password'], $_POST['confirmPassword'], 
            $_POST['first_name'], $_POST['last_name'], $_POST['email'], time(), 0, NEW_USER_ENABLED );
    
    $errors = array ();
    $errors = array_merge( SignUpFormValidator::validateRequiredFields( $user ), 
            SignUpFormValidator::validate( $user ) );
    
    // See if user is unique
    try
    {
        if ( $MEMBER_DB_MANAGER->loadMemberByUsername( $user->getUsername() ) )
        {
            $errors[] .= sprintf( 'Username \'%s\' already exists!', $user->getUsername() );
        }
    } catch ( Exception $e )
    {
    }
    
    if ( count( $errors ) == 0 )
    {
        /* Process new user */
        $MEMBER_DB_MANAGER->createUser( $user );
        
        /* Let them go to the login */
        redirectIfLoggedOut();
    }
}

redirectIfLoggedIn();

?>

<!DOCTYPE HTML>
<html lang='EN'>

<?php
echo templateHead( 'Sign Up', array ( 'css/formStyle.css' ), array ( 'js/FormWidget.js' ) );
?>

<body>

    <?= templateHeader( false )?>
    <div id='content'>
        <h1>Sign Up for The best E-Commerce site ever!</h1>
        <form id='signupForm' method='POST'>
            <?= $feedback?>
            <?php if(count($errors)>0) {foreach ($errors as $error){printf("<p class='error'><span>%s</span></p>", $error);}} ?>
                
            <p>
                <label for='first_name'>First Name:</label>
                <input id='first_name' type='text' name='first_name' placeholder='Your first name'
                    <?= (isset($_POST['first_name']))? 'value="' . $_POST['first_name'] . '"':''?>
                />
            </p>
            <p>
                <label for='last_name'>Last Name:</label>
                <input id='last_name' type='text' name='last_name' placeholder='Your last name'
                    <?= (isset($_POST['last_name']))? 'value="' . $_POST['last_name'] . '"':''?>
                />
            </p>
            <p>
                <label for='email'>Email:</label>
                <input id='email' type='email' name='email' placeholder='Your email'
                    <?= (isset($_POST['email']))? 'value="' . $_POST['email'] . '"':''?>
                />
            </p>
            <p>
                <label for='username'>Username:</label>
                <input id='username' type='text' maxlength='16' name='username'
                    placeholder='username'
                    <?= (isset($_POST['username']))? 'value="' . $_POST['username'] . '"':''?>
                />
            </p>
            <p>
                <label for='password'>Password:</label>
                <input id='password' type='password' name='password' placeholder='password' />
            </p>
            <p>
                <label for='confirmPassword'>Confirm Password:</label>
                <input id='confirmPassword' type='password' name='confirmPassword'
                    placeholder='same password'
                />
            </p>
            <div id='formButtons'>
                <p>
                    <input type='submit' value='Sign Up' name='submitted' />
                    <input type='button' value='Clear'
                        onclick="$('#signupForm input[type=text], #signupForm input[type=email], #signupForm input[type=password]').val('')"
                    />
                </p>
                <p>
                    <input type='button' value='Already have a login?'
                        onclick="javascript:window.location='login.php'"
                    />
                </p>
            </div>
        </form>

    </div>
</body>

</html>