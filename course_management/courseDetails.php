<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$courseNumber = $_GET['courseNumber'];

// See if course exists
try
{
    $TAS_DB_MANAGER->loadCourseByNumber( $courseNumber );
} catch ( CourseNotFoundException $e )
{
    echo $e->getMessage();
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $errors = array ();
    
    // Clean Data
    $_POST['number'] = clean_input( $_POST['number'] );
    $_POST['name'] = clean_input( $_POST['name'] );
    
    $enrolled = array ();
    

    $course = new CourseForm( $courseNumber, $_POST['name'], $enrolled );
    
    $errors = array ();
    $errors = array_merge( CourseFormValidator::validateRequiredFields( $course ), 
            CourseFormValidator::validate( $course, false ) );
    
    if ( count( $errors ) == 0 )
    {
        // SUBMIT A CHANGE
        $TAS_DB_MANAGER->updateCourse( $course );
        
        header( 'Location: ./' );
        die();
    }
    
    foreach ( $errors as $error )
    {
        $message .= "<p>$error</p>";
    }
}

?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Course: $courseNumber Details", 
        array ( 'css/formStyle.css', 'css/detailsStyle.css', 'css/courseDetailsStyle.css' ), 
        array ( 'js/lib/jquery.tablesorter.js', 'js/lib/underscore-min.js', 'js/FormWidget.js', 
                        'js/CourseDetailsWidget.js' ) );
?>

<body>
    <?= templateHeader(true, true, true, true, true)?>
    <div id="content">
		<form method="POST" enctype="multipart/form-data">
			<div id="nice_tableBlock">
				<table id="detailsTable">
					<tbody>
						<tr id="numberRow" class="permanent">
							<td><label for="number">Course Number</label></td>
							<td><input type="text" id="number" name="number" maxlength="15" /></td>
						</tr>
						<tr id="nameRow">
							<td><label for="name">Course Name</label></td>
							<td><input type="text" id="name" name="name" maxlength="60"
								width="60" /></td>
						</tr>
						<tr id="enrolledRow">
							<td><label for="enrolled">Roster</label></td>
							<td id="enrolled">
								<div id="studentsEnrolled"></div>
								<div id="modifyEnrolled">
									<input id="addUsersButton" type="button" value="Add Users"> <input
										id="removeUsersButton" type="button" value="Remove Users">
								</div>
							</td>
						</tr>
						<tr id="topicsRow">
							<td><label for="topics">Proposed Topics</label></td>
							<td id="topics">
								<table>
									<th>User</th>
									<th>Topic Name</th>
									<th>Status</th>
								</table>
								<p>
									<input type="button" id="topicManagement"
										value="View Topics for this Course"
										onclick="location='../topic_management?courseNumber=<?=$courseNumber?>';">
								</p>
							</td>
						</tr>
                        <?php
                        if ( isset( $message ) )
                        {
                            echo "
                            <tr>
                                <td colspan='2' style='color: red'><p>$message</p></td>
                            </tr>";
                        }
                        ?>
                        <tr id="buttonsRow" class="permanent">
							<td></td>
							<td>
								<p>
									<input type="submit" id="updateFieldsButton" name="submitted"
										value="Update With New Info">
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
	</div>
	<span id="courseNumber"><?= $courseNumber ?></span>

</body>

</html>
