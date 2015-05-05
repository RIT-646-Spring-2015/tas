<?php
require_once 'util/PreparedStatementSetter.class.php';
require_once 'exceptions/UsernameNotFoundException.class.php';
require_once 'exceptions/InadequateRightsException.class.php';

require_once 'entity/mapper/UserMapper.class.php';
require_once 'entity/mapper/CourseMapper.class.php';
require_once 'entity/mapper/TopicMapper.class.php';

class TASServiceManager
{

    const FOREIGN_KEY_SQL = 'PRAGMA foreign_keys = ON;';

    /**
     */
    const SELECT_ALL_USERS_SQL = 'SELECT User.Username, Password, Enabled, FirstName, LastName, Email, DateJoined, LastOnline, RoleName FROM User LEFT JOIN UserRole ON User.Username=UserRole.Username';

    /**
     */
    const SELECT_ALL_ROLES_SQL = 'SELECT Name FROM Role';

    /**
     */
    const SELECT_ALL_STATUSES_SQL = 'SELECT Name FROM Status';

    /**
     */
    const SELECT_ALL_COURSES_SQL = 'SELECT Number, Name, Username FROM Course LEFT JOIN UserCourse ON Course.Number=UserCourse.CourseNumber';

    /**
     */
    const SELECT_ALL_TOPICS_SQL = 'SELECT Name, Link, SubmissionDate, Blacklisted, Status FROM Topic';

    /**
     */
    const QUERY_NUMBER_OF_USERS_SQL = 'SELECT COUNT(Username) FROM User WHERE Username = ?';

    /**
     */
    const QUERY_USER_BY_USERNAME = 'SELECT User.Username, Password, Enabled, FirstName, LastName, Email, DateJoined, LastOnline, RoleName FROM User LEFT JOIN UserRole ON User.Username=UserRole.Username WHERE User.Username = ?';

    /**
     */
    const NEW_USER_SQL = 'INSERT INTO User (Username, Password, Enabled, FirstName, LastName, Email) VALUES ( ?, ?, ?, ?, ?, ? )';

    /**
     */
    const NEW_USER_ROLE_SQL = 'INSERT INTO UserRole (Username, RoleName) VALUES( ?, ? )';

    /**
     */
    const DELETE_USER_SQL = 'DELETE FROM User WHERE Username = ?';

    /**
     */
    const DELETE_USER_AUTHORITIES_SQL = 'DELETE FROM UserRole WHERE Username = ?';

    /**
     */
    const UPDATE_USER_SQL = 'UPDATE User SET Enabled = ?, FirstName = ?, LastName = ?, Email = ? WHERE Username = ?';

    /**
     */
    const UPDATE_PASSWORD_SQL = 'UPDATE User SET Password = ? WHERE Username = ?';

    /**
     */
    const UPDATE_USER_LAST_ONLINE_SQL = 'UPDATE User SET LastOnline = DATETIME(\'NOW\', \'LOCALTIME\') WHERE Username = ?';

    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLE_PROFESSOR = 'ROLE_PROFESSOR';

    const ROLE_TA = 'ROLE_TA';

    const ROLE_STUDENT = 'ROLE_STUDENT';

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
        
        $this->insertUserAuthorities( $user, self::ROLE_STUDENT );
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
        if ( !$this->isAdmin() && in_array( 'ROLE_ADMIN', $user->getAuthorities() ) )
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

    public function deleteUser( $username )
    {
        try
        {
            if ( $this->checkForAdminRights( $this->loadUserByUsername( $username ) ) )
                return;
            
            $this->failIfNotAdmin();
            
            $this->deleteUserAuthorities( $username );
            
            $stmt = $this->getDB()->prepare( self::DELETE_USER_SQL );
            $stmt->bindParam( 1, $username, SQLITE3_TEXT );
            
            $stmt->execute();
            
            $PRODUCT_DB_MANAGER->deleteUserCart( $username );
        } catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    private function insertUserAuthorities( UserDetails $user, $auth )
    {
        $stmt = $this->getDB()->prepare( self::NEW_USER_ROLE_SQL );
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
        
        if ( $currentUser->getUsername() == $username && $currentUser->getPassword() != $oldPassword )
        {
            throw new Exception( 'Old password does not match.' );
        } else if ( !in_array( 'ROLE_ADMIN', $currentUser->getAuthorities() ) )
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

    public function getAvailableAuthorities()
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
        return $user != null && $user->isEnabled() && $user->hasRole( self::ROLE_ADMIN );
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
        $stmt = $this->getDB()->prepare( self::QUERY_USER_BY_USERNAME );
        $stmt->bindParam( 1, $username, SQLITE3_TEXT );
        
        $result = $stmt->execute();
        
        $users = UserMapper::extractData( $result );
        
        if ( count( $users ) == 0 )
        {
            throw new UsernameNotFoundException( $username );
        }
        
        return $users[$username];
    }
}
?>
