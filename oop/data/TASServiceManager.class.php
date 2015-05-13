<?php
require_once 'util/PreparedStatementSetter.class.php';
require_once 'exceptions/UsernameNotFoundException.class.php';
require_once 'exceptions/CourseNotFoundException.class.php';
require_once 'exceptions/TopicNotFoundException.class.php';
require_once 'exceptions/InadequateRightsException.class.php';

require_once 'entity/mapper/UserMapper.class.php';
require_once 'entity/mapper/CourseMapper.class.php';
require_once 'entity/mapper/TopicMapper.class.php';

class TASServiceManager
{

    const FOREIGN_KEY_SQL = 'PRAGMA foreign_keys = ON;';

    /**
     */
    const SELECT_ALL_USERS_SQL = 'SELECT User.Username, Password, Enabled, FirstName, LastName, Email, DateJoined, LastOnline, AuthorityName, CourseNumber, Role FROM User LEFT JOIN UserAuthority ON User.Username=UserAuthority.Username LEFT JOIN UserCourse ON User.Username=UserCourse.Username;';

    /**
     */
    const SELECT_ALL_AUTHORITIES_SQL = 'SELECT Name FROM Authority;';

    /**
     */
    const SELECT_ALL_STATUSES_SQL = 'SELECT Name FROM Status;';

    /**
     */
    const SELECT_ALL_COURSES_SQL = 'SELECT Number, Course.Name AS CourseName, Username, Role, Topic.Name AS TopicName, SubmittingUsername FROM Course LEFT JOIN UserCourse ON Course.Number=UserCourse.CourseNumber LEFT JOIN Topic ON Course.Number=Topic.CourseNumber;';

    /**
     */
    const SELECT_ALL_ROLES_SQL = 'SELECT Name FROM Role;';

    /**
     */
    const SELECT_ALL_TOPICS_SQL = 'SELECT Name, SubmittingUsername, CourseNumber, Link, SubmissionDate, Blacklisted, Status FROM Topic;';

    /**
     */
    const QUERY_NUMBER_OF_USERS_SQL = 'SELECT COUNT(Username) FROM User WHERE Username = ?;';

    /**
     */
    const QUERY_USER_BY_USERNAME_SQL = 'SELECT User.Username, Password, Enabled, FirstName, LastName, Email, DateJoined, LastOnline, AuthorityName FROM User LEFT JOIN UserAuthority ON User.Username=UserAuthority.Username WHERE User.Username = ?;';

    /**
     */
    const QUERY_COURSE_BY_NUMBER_SQL = 'SELECT Number, Course.Name AS CourseName, Username, Role, Topic.Name AS TopicName, SubmittingUsername FROM Course LEFT JOIN UserCourse ON Course.Number=UserCourse.CourseNumber LEFT JOIN Topic ON Course.Number=Topic.CourseNumber WHERE Number = ?;';

    /**
     */
    const QUERY_TOPIC_BY_NAME_SQL = 'SELECT Name, SubmittingUsername, CourseNumber, Link, SubmissionDate, Blacklisted, Status FROM Topic WHERE Name = ?;';

    /**
     */
    const QUERY_TOPICS_BY_COURSE_SQL = 'SELECT Name, SubmittingUsername, CourseNumber, Link, SubmissionDate, Blacklisted, Status FROM Topic WHERE CourseNumber = ?;';

    /**
     */
    const NEW_USER_SQL = 'INSERT INTO User (Username, Password, Enabled, FirstName, LastName, Email) VALUES ( ?, ?, ?, ?, ?, ? );';

    /**
     */
    const NEW_USER_AUTHORITY_SQL = 'INSERT INTO UserAuthority (Username, AuthorityName) VALUES( ?, ? );';

    /**
     */
    const NEW_COURSE_SQL = 'INSERT INTO Course (Number, Name) VALUES ( ?, ? );';

    /**
     */
    const NEW_USER_COURSE_SQL = 'INSERT INTO UserCourse (Username, CourseNumber, Role) VALUES ( ?, ?, ? );';

    /**
     */
    const NEW_TOPIC_SQL = 'INSERT INTO Topic (Name, SubmittingUsername, CourseNumber, Link) VALUES ( ?, ?, ?, ? );';

    /**
     */
    const DELETE_USER_SQL = 'DELETE FROM User WHERE Username = ?;';

    /**
     */
    const DELETE_USER_AUTHORITIES_SQL = 'DELETE FROM UserAuthority WHERE Username = ?;';

    /**
     */
    const DELETE_COURSE_SQL = 'DELETE FROM Course WHERE Number = ?;';

    /**
     */
    const DELETE_USER_COURSE_USER_TOPIC_SQL = 'DELETE FROM UserCourseUserTopic WHERE Username = ? AND UserTopicName = ? AND UserCourseNumber = ?;';

    /**
     */
    const DELETE_USER_COURSE_SQL = 'DELETE FROM UserCourse WHERE Username = ? AND CourseNumber = ?;';

    /**
     */
    const DELETE_TOPIC_SQL = 'DELETE FROM Topic WHERE Name = ?;';

    /**
     */
    const UPDATE_USER_SQL = 'UPDATE User SET Enabled = ?, FirstName = ?, LastName = ?, Email = ? WHERE Username = ?;';

    /**
     */
    const UPDATE_COURSE_SQL = 'UPDATE Course SET Name = ? WHERE Number = ?;';

    /**
     */
    const UPDATE_TOPIC_SQL = 'UPDATE Topic SET SubmittingUsername = ?, CourseNumber = ?, Link = ?, SubmissionDate = ?, Blacklisted = ?, Status = ?, WHERE Name = ?;';

    /**
     */
    const UPDATE_PASSWORD_SQL = 'UPDATE User SET Password = ? WHERE Username = ?;';

    /**
     */
    const UPDATE_USER_LAST_ONLINE_SQL = 'UPDATE User SET LastOnline = DATETIME(\'NOW\', \'LOCALTIME\') WHERE Username = ?;';

    const AUTHORITY_ADMIN = 'ADMIN';

    const ROLE_PROFESSOR = 'PROFESSOR';

    const ROLE_TA = 'TA';

    const ROLE_STUDENT = 'STUDENT';

    const TOPIC_STATUS_SUBMITTED = 'SUBMITTED';

    const TOPIC_STATUS_APPROVED = 'APPROVED';

    const TOPIC_STATUS_REJECTED = 'REJECTED';

    /**
     * This is a Singleton architecture I am trying to acheive.
     */
    public static function &getInstance()
    {
        static $instance = null;
        if ( null === $instance )
        {
            $instance = new static();
        }
        
        return $instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    private function getDB()
    {
        try
        {
            $db = new SQLite3( TAS_DB );
            $db->exec( self::FOREIGN_KEY_SQL );
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
        return $db;
    }

    public function createUser( UserForm $user )
    {
        $stmt = $this->getDB()->prepare( self::NEW_USER_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($user ) {
                    $ps->bindValue( 1, $user->getUsername(), SQLITE3_TEXT );
                    $ps->bindValue( 2, sha1( $user->getPassword() ), SQLITE3_TEXT );
                    $ps->bindValue( 3, NEW_USER_ENABLED, SQLITE3_INTEGER );
                    $ps->bindValue( 4, $user->getFirstName(), SQLITE3_TEXT );
                    $ps->bindValue( 5, $user->getLastName(), SQLITE3_TEXT );
                    $ps->bindValue( 6, $user->getEmail(), SQLITE3_TEXT );
                }, $stmt );
    }

    public function createCourse( CourseForm $course )
    {
        $stmt = $this->getDB()->prepare( self::NEW_COURSE_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($course ) {
                    $ps->bindValue( 1, $course->getNumber(), SQLITE3_TEXT );
                    $ps->bindValue( 2, $course->getName(), SQLITE3_TEXT );
                }, $stmt );
    }

    public function createTopic( TopicForm $topic )
    {
        $stmt = $this->getDB()->prepare( self::NEW_TOPIC_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($topic ) {
                    $ps->bindValue( 1, $topic->getName(), SQLITE3_TEXT );
                    $ps->bindValue( 2, $topic->getSubmittingUsername(), SQLITE3_TEXT );
                    $ps->bindValue( 3, $topic->getCourseNumber(), SQLITE3_TEXT );
                    $ps->bindValue( 4, $topic->getLink(), SQLITE3_TEXT );
                }, $stmt );
    }

    public function loggedIn( User $user )
    {
        echo htmlentities( $user->toString() );
        // Update the last time this user was online
        $stmt = $this->getDB()->prepare( self::UPDATE_USER_LAST_ONLINE_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($user ) {
                    $ps->bindValue( 1, $user->getUsername() );
                }, $stmt );
        
        // set session variable
        $_SESSION[USER] = $user;
    }

    public function updateUser( UserDetails $user )
    {
        if ( $this->getCurrentUser()->getUsername() != $user->getUsername() )
        {
            $this->failIfNotAdmin();
        }
        
        // See if user is not admin and trying to become an admin
        if ( !$this->isAdmin() && $user->hasAuthority( self::AUTHORITY_ADMIN ) )
        {
            throw new InadequateRightsException( 
                    'Non-administrator cannot make him/herself an administrator!' );
        }
        
        $stmt = $this->getDB()->prepare( self::UPDATE_USER_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($user ) {
                    $ps->bindValue( 1, $user->isEnabled(), SQLITE3_INTEGER );
                    $ps->bindValue( 2, $user->getFirstName() );
                    $ps->bindValue( 3, $user->getLastName() );
                    $ps->bindValue( 4, $user->getEmail() );
                    $ps->bindValue( 5, $user->getUsername() );
                }, $stmt );
        
        $this->deleteUserAuthorities( $user->getUsername() );
        foreach ( $user->getAuthorities() as $auth )
        {
            $this->insertUserAuthorities( $user, $auth );
        }
    }

    public function updateCourse( CourseDetails $course )
    {
        $this->failIfNotAdmin();
        
        $stmt = $this->getDB()->prepare( self::UPDATE_COURSE_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($course ) {
                    $ps->bindValue( 1, $course->getName() );
                    $ps->bindValue( 2, $course->getNumber() );
                }, $stmt );
    }

    public function updateTopic( TopicDetails $topic )
    {
        if ( $this->getCurrentUser()->getUsername() != $topic->getSubmittingUsername() )
        {
            $this->failIfNotAdmin( 
                    'To update a topic, you must be the one who submitted the topic or be an administrator.' );
        }
        
        $stmt = $this->getDB()->prepare( self::UPDATE_TOPIC_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($topic ) {
                    $ps->bindValue( 1, $topic->getSubmittingUsername() );
                    $ps->bindValue( 2, $topic->getCourseNumber() );
                    $ps->bindValue( 3, $topic->getLink() );
                    $ps->bindValue( 4, $topic->getSubmissionDate() );
                    $ps->bindValue( 5, $topic->getBlacklisted() );
                    $ps->bindValue( 6, $topic->getStatus() );
                    $ps->bindValue( 7, $topic->getName() );
                }, $stmt );
    }

    public function deleteUser( $username )
    {
        try
        {
            if ( $this->checkForAdminRights( $this->loadUserByUsername( $username ) ) )
                return;
            
            $this->failIfNotAdmin();
            
            $user = $this->loadUserByUsername( $username );
            
            // Remove this user from all their courses
            foreach ( $user->getCoursesEnrolledIn() as $courseNumber )
            {
                $this->removeUserFromCourse( $username, $courseNumber );
            }
            
            // Remove this user's topic proposals
            foreach ( $TAS_DB_MANAGER->getTopics() as $topic )
            {
                if ( $topic->getSubmittingUser() == $username )
                {
                    $this->deleteTopic( $topic->getName() );
                }
            }
            
            $this->deleteUserAuthorities( $username );
            
            $stmt = $this->getDB()->prepare( self::DELETE_USER_SQL );
            $stmt->bindParam( 1, $username, SQLITE3_TEXT );
            
            $stmt->execute();
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    public function deleteCourse( $courseNumber )
    {
        // Get the course to delete
        $course = $this->loadCourseByNumber( $courseNumber );
        
        // unenroll users
        foreach ( $course->getEnrolled() as $username )
            $this->removeUserFromCourse( $username, $courseNumber );
            
            // delete all topics for a course
        foreach ( $course->getTopics() as $topicName )
            $this->deleteTopic( $topicName );
        
        try
        {
            $this->failIfNotAdmin();
            
            $stmt = $this->getDB()->prepare( self::DELETE_COURSE_SQL );
            $stmt->bindParam( 1, $courseNumber, SQLITE3_TEXT );
            
            $stmt->execute();
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    public function deleteTopic( $topicName )
    {
        // Get the topic to delete
        $topic = $this->loadTopicByName( $topicName );
        
        try
        {
            if ( $this->getCurrentUser()->getUsername() != $topic->getSubmittingUsername() )
                $this->failIfNotAdmin();
            
            $stmt = $this->getDB()->prepare( self::DELETE_TOPIC_SQL );
            $stmt->bindParam( 1, $topicName, SQLITE3_TEXT );
            
            $stmt->execute();
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    public function addUserToCourse( $username, $courseNumber, $role )
    {
        $user = $this->getCurrentUser();
        $course = $this->loadCourseByNumber( $courseNumber );
        $enrolled = $course->getEnrolled();
        
        try
        {
            $error = false;
            // if the user being added is not the current user, and the current user is a
            // professor: OK
            if ( $username != $user->getUsername() &&
                     array_key_exists( $user->getUsername(), $enrolled ) &&
                     $enrolled[$user->getUsername()] == self::ROLE_PROFESSOR )
            {
                goto goodToGo;
            } else
            {
                $error = true;
            }
            
            // if the user being added is not the current user, and the current user is a TA,
            // and the user is being added a student: OK
            if ( $username != $user->getUsername() &&
                     array_key_exists( $user->getUsername(), $enrolled ) &&
                     $enrolled[$user->getUsername()] == self::ROLE_TA && $role == self::ROLE_STUDENT )
            {
                goto goodToGo;
            } else
            {
                $error = true;
            }
            
            // if the user being added is the current user, and the user is being added as a
            // student: OK
            if ( $username == $user->getUsername() && $role == self::ROLE_STUDENT )
            {
                goto goodToGo;
            } else
            {
                $error = true;
            }
            
            if ( $error )
            {
                die( 'Non TA/Professor cannot add him/herself to course as non-student' );
            }
            
            // if the current user is an admin: OK
            $this->failIfNotAdmin();
            
            // if the user is not already enrolled in the course: OK
            if ( !array_key_exists( $user->getUsername(), $enrolled ) )
            {
                die( 'user is already enrolled in course' );
            }
            
            goodToGo:
            
            $stmt = $this->getDB()->prepare( self::NEW_USER_COURSE_SQL );
            $stmt->bindParam( 1, $username, SQLITE3_TEXT );
            $stmt->bindParam( 2, $courseNumber, SQLITE3_TEXT );
            $stmt->bindParam( 3, $role, SQLITE3_TEXT );
            
            $stmt->execute();
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    public function removeUserFromCourse( $username, $courseNumber )
    {
        $user = $this->getCurrentUser();
        $course = $this->loadCourseByNumber( $courseNumber );
        $enrolled = $course->getEnrolled();
        
        try
        {
            if ( !array_key_exists( $username, $course->getEnrolled() ) && ( array_key_exists( 
                    $user->getUsername(), $enrolled ) &&
                     !$enrolled[$user->getUsername()] != self::ROLE_PROFESSOR ) )
                $this->failIfNotAdmin();
            
            $stmt = $this->getDB()->prepare( self::DELETE_USER_COURSE_SQL );
            $stmt->bindParam( 1, $username, SQLITE3_TEXT );
            $stmt->bindParam( 2, $courseNumber, SQLITE3_TEXT );
            
            $stmt->execute();
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    public function removeTopicProposal( $username, $topicName, $courseNumber )
    {
        $currentUser = $this->getCurrentUser();
        
        if ( $currentUser->getUsername() != $username && !$currentUser->hasAuthority( 
                self::AUTHORITY_ADMIN ) )
        {
            throw new Exception( 'You do not have the authority to delete someone else\'s topic' );
        }
        
        $stmt = $this->getDB()->prepare( self::DELETE_USER_COURSE_USER_TOPIC_SQL );
        $stmt->bindParam( 1, $username, SQLITE3_TEXT );
        $stmt->bindParam( 2, $topicName, SQLITE3_TEXT );
        $stmt->bindParam( 3, $courseNumber, SQLITE3_TEXT );
        
        $stmt->execute();
    }

    private function insertUserAuthorities( UserDetails $user, $auth )
    {
        $stmt = $this->getDB()->prepare( self::NEW_USER_AUTHORITY_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($user, $auth ) {
                    $ps->bindValue( 1, $user->getUsername(), SQLITE3_TEXT );
                    $ps->bindValue( 2, $auth, SQLITE3_TEXT );
                }, $stmt );
    }

    private function deleteUserAuthorities( $username )
    {
        $stmt = $this->getDB()->prepare( self::DELETE_USER_AUTHORITIES_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($username ) {
                    $ps->bindValue( 1, $username, SQLITE3_TEXT );
                }, $stmt );
    }

    public function changePassword( $oldPassword, $newPassword, $username = null )
    {
        $currentUser = $this->getCurrentUser();
        
        if ( $username == null )
        {
            $username = $currentUser->getUsername();
        }
        
        if ( $currentUser == null )
        {
            // This would indicate bad coding somewhere
            throw new Exception( 
                    'Can\'t change password as no Authentication was found in context for current user.' );
        }
        
        if ( $currentUser->getUsername() == $username )
        {
            if ( $currentUser->getPassword() != $oldPassword )
                throw new Exception( 'Old password does not match.' );
        } else if ( !$currentUser->hasAuthority( self::AUTHORITY_ADMIN ) )
        {
            throw new Exception( "Only Administrators can change the password of another user" );
        }
        
        $stmt = $this->getDB()->prepare( self::UPDATE_PASSWORD_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use($newPassword, $username ) {
                    $ps->bindValue( 1, sha1( $newPassword ), SQLITE3_TEXT );
                    $ps->bindValue( 2, $username, SQLITE3_TEXT );
                }, $stmt );
        return;
    }

    public function getUsers()
    {
        return UserMapper::extractData( $this->getDB()->query( self::SELECT_ALL_USERS_SQL ) );
    }

    public function getCourses()
    {
        return CourseMapper::extractData( $this->getDB()->query( self::SELECT_ALL_COURSES_SQL ) );
    }

    public function getTopics()
    {
        return TopicMapper::extractData( $this->getDB()->query( self::SELECT_ALL_TOPICS_SQL ) );
    }

    public function getAvailableAuthorities()
    {
        $results = $this->getDB()->query( self::SELECT_ALL_AUTHORITIES_SQL );
        
        $authorities = array ();
        
        while ( $res = $results->fetchArray( SQLITE3_ASSOC ) )
            $authorities[] = $res['Name'];
        
        return $authorities;
    }

    public function getAvailableRoles()
    {
        $results = $this->getDB()->query( self::SELECT_ALL_ROLES_SQL );
        
        $roles = array ();
        
        while ( $res = $results->fetchArray( SQLITE3_ASSOC ) )
            $roles[] = $res['Name'];
        
        return $roles;
    }

    public function getAvailableStatuses()
    {
        $results = $this->getDB()->query( self::SELECT_ALL_STATUSES_SQL );
        
        $statuses = array ();
        
        while ( $res = $results->fetchArray( SQLITE3_ASSOC ) )
            $statuses[] = $res['Name'];
        
        return $statuses;
    }

    /**
     * This method is here mostly for convention in a system
     * where accessing the current user is more difficult.
     * In PHP it is accessible through the $_SESSION so that
     * is what is used in code elsewhere.
     */
    public function getCurrentUser()
    {
        return $_SESSION[USER];
    }

    private function checkForAdminRights( User &$user )
    {
        // DEBUG
        // echo htmlentities( $user->toString() ) . "<br>";
        return $user != null && $user->isEnabled() && $user->hasAuthority( self::AUTHORITY_ADMIN );
    }

    public function isAdmin()
    {
        $user = $_SESSION[USER];
        
        return $this->checkForAdminRights( $user );
    }

    public function failIfNotAdmin( $message = 'A non-administrator cannot do that!' )
    {
        $user = $this->getCurrentUser();
        
        if ( !$this->checkForAdminRights( $user ) )
        {
            throw new InadequateRightsException( $message );
        }
        
        return $user;
    }

    /**
     * Executes the SQL <tt>usersByUsernameQuery</tt> and returns a list of
     * User objects.
     * There should normally only be one matching user.
     */
    public function loadUserByUsername( $username )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_USER_BY_USERNAME_SQL );
        $stmt->bindParam( 1, $username, SQLITE3_TEXT );
        
        $result = $stmt->execute();
        
        $users = UserMapper::extractData( $result );
        
        if ( count( $users ) <= 0 )
        {
            throw new UsernameNotFoundException( $username );
        }
        
        return $users[$username];
    }

    public function loadCourseByNumber( $courseNumber )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_COURSE_BY_NUMBER_SQL );
        $stmt->bindParam( 1, $courseNumber, SQLITE3_TEXT );
        
        $result = $stmt->execute();
        
        $courses = CourseMapper::extractData( $result );
        
        if ( count( $courses ) <= 0 )
        {
            throw new CourseNotFoundException( $courseNumber );
        }
        
        return $courses[$courseNumber];
    }

    public function loadTopicByName( $topicName )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_TOPIC_BY_NAME_SQL );
        $stmt->bindParam( 1, $topicName, SQLITE3_TEXT );
        
        $result = $stmt->execute();
        
        $topics = TopicMapper::extractData( $result );
        
        if ( count( $topics ) <= 0 )
        {
            throw new TopicNotFoundException( $topicName );
        }
        
        return $topics[$topicName];
    }

    public function loadTopicsByCourseNumber( $courseNumber )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_TOPICS_BY_COURSE_SQL );
        $stmt->bindParam( 1, $courseNumber, SQLITE3_TEXT );
        
        $result = $stmt->execute();
        
        $topics = TopicMapper::extractData( $result );
        
        return $topics;
    }
}
?>
