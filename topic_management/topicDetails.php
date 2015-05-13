<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$topicName = $_GET['topicName'];

// See if topic exists
try
{
    $thisTopic = $TAS_DB_MANAGER->loadTopicByName( $topicName );
} catch ( TopicNotFoundException $e )
{
    echo $e->getMessage();
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $errors = array ();
    
    // Clean Data
    $_POST['link'] = clean_input( $_POST['link'] );
    
    $enrolled = array ();
    
    $topic = new TopicForm( $topicName, $_POST['submittingUsername'], $thisTopic->getCourseNumber(), 
            $_POST['link'], $_POST['submissionDate'], $_POST['blacklisted'] == 'on' ? true : false, 
            $_POST['status'] );
    
    $errors = array ();
    $errors = array_merge( TopicFormValidator::validateRequiredFields( $topic ), 
            TopicFormValidator::validate( $topic ) );
    
    if ( count( $errors ) == 0 )
    {
        // SUBMIT A CHANGE
        $TAS_DB_MANAGER->updateTopic( $topic );
        
        header( 'Location: ' . $_POST['referer'] );
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
echo templateHead( "Topic: $topicName Details", 
        array ( 'css/formStyle.css', 'css/detailsStyle.css', 'css/topicDetailsStyle.css' ), 
        array ( 'js/lib/jquery.tablesorter.js', 'js/lib/underscore-min.js', 'js/FormWidget.js', 
                        'js/TopicDetailsWidget.js' ) );
?>

<body>
    <?= templateHeader(true, true, true, true, true)?>
    <div id="content">
		<form method="POST" enctype="multipart/form-data">
			<input type="text" style="display: none;" name="referer"
				value="<?=isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : './'?>">
			<div id="nice_tableBlock">
				<table id="detailsTable">
					<tbody>
						<tr id="nameRow" class="permanent">
							<td><label for="name">Topic Name</label></td>
							<td><input type="text" id="name" name="name" maxlength="60" /></td>
						</tr>
						<tr id="submittingUsernameRow" class="permanent">
							<td><label for="submittingUsername">Submitting User</label></td>
							<td><p>
									<input type="text" id="submittingUsername"
										name="submittingUsername" maxlength="20" />
								</p>
								<p>
									<input type="text" id="userFullName" name="userFullName"
										maxlength="20" />
								</p></td>
						</tr>
						<tr id="courseNumberRow" class="permanent">
							<td><label for="courseNumber">Course Number</label></td>
							<td><input type="text" id="courseNumber" name="courseNumber"
								maxlength="15" /></td>
						</tr>
						<tr id="linkRow">
							<td><label for="link">Link</label></td>
							<td><input type="url" id="link" name="link" maxlength="500" /></td>
						</tr>
						<tr id="submissionDateRow" class="permanent">
							<td><label for="submissionDate">Date Submitted</label></td>
							<td><input type="text" id="submissionDate" name="submissionDate" /></td>
						</tr>
						<tr id="statusRow">
							<td><label for="status">Status</label></td>
							<td><select id='status' name='status' required>
            					<?php
                foreach ( $TAS_DB_MANAGER->getAvailableStatuses() as $status )
                {
                    printf( 
                            "<option value='$status'" .
                                     ( ( isset( $_POST['status'] ) && $_POST['status'] == $status ) ? "selected" : "" ) .
                                     ">$status</option>" );
                }
                ?>
            				</select></td>
						</tr>
						<tr id="blacklistedRow">
							<td><label for="blacklisted">Blacklisted</label></td>
							<td><input type="checkbox" id="blacklisted" name="blacklisted" /></td>
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
	<span id="topicName"><?= $topicName ?></span>

</body>

</html>
