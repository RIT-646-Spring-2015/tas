<?php
require '../lib/lib_tas.php';

$username = getActingUsername( "You cannot add a topic for another user!" );
if ( isset( $_GET['courseNumber'] ) )
{
    $courseNumber = $_GET['courseNumber'];
    $topic = $TAS_DB_MANAGER->loadCourseByNumber( $courseNumber );
    $username = $username == null ? $TAS_DB_MANAGER->getCurrentUser()->getUsername() : $username;
    if ( !array_key_exists( $username, $topic->getEnrolled() ) )
    {
        try
        {
            $TAS_DB_MANAGER->failIfNotAdmin( 
                    'You cannot view topics for a course you are not enrolled in!' );
        } catch ( InadequateRightsException $e )
        {
            die( $e->getMessage() );
        }
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submitted'] )
{
    // Clean Data
    $_POST['name'] = clean_input( $_POST['name'] );
    $_POST['link'] = clean_input( $_POST['link'] );
    
    $topic = new TopicForm( $_POST['name'], $_POST['submittingUsername'], $_POST['courseNumber'], 
            $_POST['link'], "", "", "" );
    
    $errors = array_merge( TopicFormValidator::validateRequiredFields( $topic ), 
            TopicFormValidator::validate( $topic ) );
    
    // See if topic is unique
    try
    {
        if ( $TAS_DB_MANAGER->loadTopicByName( $topic->getName() ) )
        {
            $errors[] .= sprintf( 'Topic \'%s\' already exists!', $topic->getName() );
        }
    } catch ( Exception $e )
    {
    }
    
    if ( count( $errors ) == 0 )
    {
        /* Process new topic */
        $TAS_DB_MANAGER->createTopic( $topic );
        
        /* Back to Topic Management */
        header( 'Location: ' . SITE_ROOT . '/topic_management' . (isset($_GET['username'])? "?username=" .$_GET['username']:'' ) );
        die();
    }
}

?>

<!DOCTYPE HTML>
<html lang='EN'>

<?php
echo templateHead( 'Add Topic', array ( 'css/formStyle.css' ), array ( 'js/FormWidget.js' ) );
?>

<body>

    <?= templateHeader( true, true, true, true, true )?>
    <div id='content'>
		<h1>Add a new Topic to TAS</h1>
		<form id='addTopicForm' method='POST'>
            <?= $feedback?>
            <?php if(count($errors)>0) {foreach ($errors as $error){printf("<p class='error'><span>%s</span></p>", $error);}} ?>
                
            <p>
				<label for='name'>Topic Name:</label> <input id='name' type='text'
					name='name' placeholder='Topic name' maxlength="15"
					<?= (isset($_POST['name']))? 'value="' . $_POST['name'] . '"':''?> />
			</p>
			<p>
				<label for='submittingUsername'>Submitting User:</label> <input
					id='submittingUsername' type='text' name='submittingUsername'
					placeholder='Submitting username' maxlength="20"
					<?= (isset($_POST['submittingUsername']))? 'value="' . $_POST['submittingUsername'] . '"':'value="' . $username . '"'?>
					<?= !$TAS_DB_MANAGER->isAdmin()? 'readonly':'' ?> />
			</p>
			<p>
				<label for='name'>Course Number:</label> <select id='courseNumber'
					name='courseNumber' required>
					<?php
                        foreach ( $TAS_DB_MANAGER->getCourses() as $course )
                        {
                            if ( !$TAS_DB_MANAGER->isAdmin() &&
                                    !array_key_exists( $username, $course->getEnrolled() ) )
                                continue;
                            $courseNum = $course->getNumber();
                            printf( 
                                    "<option value='$courseNum'" .
                                             ( ( $_POST['courseNumber'] == $courseNum ) ? "selected" : "" ) .
                                             ">$courseNum</option>" );
                        }
                    ?>
				</select>
			</p>
			<p>
				<label for='link'>Link:</label> <input id='link' type='url'
					name='link' placeholder='Topic Link' maxlength="500"
					<?= (isset($_POST['link']))? 'value="' . $_POST['link'] . '"':""?> />
			</p>
			<div id='formButtons'>
				<p>
					<input type='submit' value='Submit Topic' name='submitted' />
				</p>
				<p>
					<input type='button' value='Cancel'
						onclick="javascript:window.location='./'" />
				</p>
			</div>
		</form>

	</div>
</body>

</html>