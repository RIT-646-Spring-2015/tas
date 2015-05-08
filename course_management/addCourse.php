<?php
require '../lib/lib_tas.php';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submitted'] )
{
    // Clean Data
    $_POST['number'] = clean_input( $_POST['number'] );
    $_POST['name'] = clean_input( $_POST['name'] );
    
    $course = new CourseForm( $_POST['number'], $_POST['name'], array() );
    
    $errors = array ();
    $errors = array_merge( CourseFormValidator::validateRequiredFields( $course ), 
            CourseFormValidator::validate( $course ) );
    
    // See if course is unique
    try
    {
        if ( $TAS_DB_MANAGER->loadCourseByNumber( $course->getNumber() ) )
        {
            $errors[] .= sprintf( 'Course \'%s: %s\' already exists!', $course->getNumber(), 
                    $course->getName() );
        }
    } catch ( Exception $e )
    {
    }
    
    if ( count( $errors ) == 0 )
    {
        /* Process new course */
        $TAS_DB_MANAGER->createCourse( $course );
        
        /* Back to Course Management */
        header( 'Location: ' . SITE_ROOT . '/course_management' );
        die();
    }
}

?>

<!DOCTYPE HTML>
<html lang='EN'>

<?php
echo templateHead( 'Add Course', array ( 'css/formStyle.css' ), array ( 'js/FormWidget.js' ) );
?>

<body>

    <?= templateHeader( true, true, true, true, true )?>
    <div id='content'>
		<h1>Add a new Course to TAS</h1>
		<form id='addCourseForm' method='POST'>
            <?= $feedback?>
            <?php if(count($errors)>0) {foreach ($errors as $error){printf("<p class='error'><span>%s</span></p>", $error);}} ?>
                
            <p>
				<label for='number'>Course Number:</label> <input id='number'
					type='text' name='number' placeholder='Course number'
					maxlength="15"
					<?= (isset($_POST['number']))? 'value="' . $_POST['number'] . '"':''?> />
			</p>
			<p>
				<label for='name'>Course Name:</label> <input id='name' type='text'
					name='name' placeholder='Course name' maxlength="60"
					<?= (isset($_POST['name']))? 'value="' . $_POST['name'] . '"':''?> />
			</p>
			<div id='formButtons'>
				<p>
					<input type='submit' value='Add Course' name='submitted' /> <input
						type='button' value='Clear'
						onclick="$('#addCourseForm input[type=text]').val('')" />
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