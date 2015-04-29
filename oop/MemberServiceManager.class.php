<?php
require_once 'PreparedStatementSetter.class.php';
require_once 'exceptions/UsernameNotFoundException.class.php';
require_once 'exceptions/InadequateRightsException.class.php';

class MemberServiceManager
{

    /**
     */
    const QUERY_NUMBER_OF_USERS_SQL = 'SELECT COUNT(username) FROM users WHERE username = ?';

    /**
     */
    const QUERY_USER_BY_USERNAME = 'SELECT users.username, password, enabled, first_name, last_name, email, date_joined, last_online, role FROM users LEFT JOIN user_role ON users.username=user_role.username WHERE users.username = ?';

    /**
     */
    const SELECT_ALL_USERS_SQL = 'SELECT users.username, password, enabled, first_name, last_name, email, date_joined, last_online, role FROM users LEFT JOIN user_role ON users.username=user_role.username';

    /**
     */
    const SELECT_ALL_ROLES_SQL = 'SELECT role FROM roles';

    /**
     */
    const NEW_USER_SQL = 'INSERT INTO users (username, password, enabled, first_name, last_name, email) VALUES ( ?, ?, ?, ?, ?, ? )';

    /**
     */
    const NEW_USER_ROLE_SQL = 'INSERT INTO user_role (username, role) VALUES( ?, ? )';

    /**
     */
    const DELETE_USER_SQL = 'DELETE FROM users WHERE username = ?';

    /**
     */
    const DELETE_USER_AUTHORITIES_SQL = 'DELETE FROM user_role WHERE username = ?';

    /**
     */
    const UPDATE_USER_SQL = 'UPDATE users SET enabled = ?, first_name = ?, last_name = ?, email = ? WHERE username = ?';

    /**
     */
    const UPDATE_PASSWORD_SQL = 'UPDATE users SET password = ? WHERE username = ?';

    /**
     */
    const UPDATE_USER_LAST_ONLINE_SQL = 'UPDATE users SET last_online = DATETIME(\'NOW\', \'LOCALTIME\') WHERE username = ?';

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
        try {
            $db = new SQLite3( MEMBER_DB );
        } catch( Exception $e )
        {
            echo $e->getMessage();
        }
        return $db;
    }

    public function createUser( MemberForm $user )
    {
        $stmt = $this->getDB()->prepare( self::NEW_USER_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $user )
                {
                    $ps->bindValue( 1, $user->getUsername(), SQLITE3_TEXT );
                    $ps->bindValue( 2, sha1( $user->getPassword() ), SQLITE3_TEXT );
                    $ps->bindValue( 3, NEW_USER_ENABLED, SQLITE3_INTEGER );
                    $ps->bindValue( 4, $user->getFirstName(), SQLITE3_TEXT );
                    $ps->bindValue( 5, $user->getLastName(), SQLITE3_TEXT );
                    $ps->bindValue( 6, $user->getEmail(), SQLITE3_TEXT );
                }, $stmt );
        
        $this->insertUserAuthorities( $user, 'ROLE_USER' );
    }

    public function loggedIn( Member $user )
    {
        // Update the last time this user was online
        $stmt = $this->getDB()->prepare( self::UPDATE_USER_LAST_ONLINE_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $user )
                {
                    $ps->bindValue( 1, $user->getUsername() );
                }, $stmt );

        // set session variable
        $_SESSION[USER] = $user;
    }

    public function updateUser( MemberDetails $user )
    {
        if ( $this->getCurrentUser()->getUsername() != $user->getUsername() )
        {
            $this->failIfNotAdmin();
        }

        // See if user is not admin and trying to become an admin
        if ( !$this->isAdmin() && in_array( 'ROLE_ADMIN', $user->getAuthorities() ) )
        {
            throw new InadequateRightsException( 'Non-administrator cannot make him/herself an administrator!' );
        }
        
        $stmt = $this->getDB()->prepare( self::UPDATE_USER_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $user )
                {
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
            if ( $this->checkForAdminRights( $this->loadMemberByUsername( $username ) ) )
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

    private function insertUserAuthorities( MemberDetails $user, $auth )
    {
        $stmt = $this->getDB()->prepare( self::NEW_USER_ROLE_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $user, $auth )
                {
                    $ps->bindValue( 1, $user->getUsername(), SQLITE3_TEXT );
                    $ps->bindValue( 2, $auth, SQLITE3_TEXT );
                }, $stmt );
    }

    private function deleteUserAuthorities( $username )
    {
        $stmt = $this->getDB()->prepare( self::DELETE_USER_AUTHORITIES_SQL );
        PreparedStatementSetter::setValuesAndExecute( 
                function ( SQLite3Stmt &$ps ) use ( $username )
                {
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
                function ( SQLite3Stmt &$ps ) use ( $newPassword, $username )
                {
                    $ps->bindValue( 1, sha1( $newPassword ), SQLITE3_TEXT );
                    $ps->bindValue( 2, $username, SQLITE3_TEXT );
                }, $stmt );
        return;
    }

    public function getUsers()
    {
        return $this->extractData( $this->getDB()->query( self::SELECT_ALL_USERS_SQL ) );
    }

    public function getAvailableAuthorities()
    {
        $results = $this->getDB()->query( self::SELECT_ALL_ROLES_SQL );
        
        $roles = array();
        
        while ( $res = $results->fetchArray( SQLITE3_ASSOC ) )
            $roles[] = $res['role'];
        
        return $roles;
    }

    /**
     * This method is here mostly for convention in a system
     *   where accessing the current user is more difficult.
     *   In PHP it is accessible through the $_SESSION so that
     *   is what is used in code elsewhere.
     */
    public function getCurrentUser()
    {
        return $_SESSION[USER];
    }

    private function checkForAdminRights( Member $user )
    {
        return $user != null && $user->isEnabled() &&
                 in_array( 'ROLE_ADMIN', $user->getAuthorities() );
    }

    public function isAdmin()
    {
        $user = $_SESSION[USER];

        return $this->checkForAdminRights( $user );
    }

    public function failIfNotAdmin( $message = 'A non-administrator cannot do that!' )
    {
        $user = $this->getCurrentUser();
        
        if ( $user == null || !$user->isEnabled() ||
                 !in_array( 'ROLE_ADMIN', $user->getAuthorities() ) )
        {
            throw new InadequateRightsException( $message );
        }
        
        return $user;
    }

    /**
     * Executes the SQL <tt>usersByUsernameQuery</tt> and returns a list of
     * Member objects.
     * There should normally only be one matching user.
     */
    public function loadMemberByUsername( $username )
    {
        $stmt = $this->getDB()->prepare( self::QUERY_USER_BY_USERNAME );
        $stmt->bindParam( 1, $username, SQLITE3_TEXT );
        
        $result = $stmt->execute();

        $users = self::extractData( $result );
        
        if ( count( $users ) == 0 )
        {
            throw new UsernameNotFoundException( $username );
        }
        
        return $users[$username];
    }

    private static function mapRow( $rs )
    {
        $roles = array ( $rs['role'] );
        
        $user = new Member( $rs['username'], $rs['password'], $rs['first_name'], $rs['last_name'], 
                $rs['email'], $rs['date_joined'], $rs['last_online'], $rs['enabled'], $roles );
        
        return $user;
    }

    private static function extractData( $rs )
    {
        $results = array();
        
        while ( $res = $rs->fetchArray( SQLITE3_ASSOC ) )
        {
            if ( !isset( $res['username'] ) )
                continue;
            
            $user = self::mapRow( $res );
            
            if ( array_key_exists( $user->getUsername(), $results ) )
            {
                $inUser = &$results[$user->getUsername()];
                
                $auths = &$inUser->getAuthorities();
                $auths = array_merge( $auths, $user->getAuthorities() );
            } else
            {
                $results[$user->getUsername()] = $user;
            }
        }

        return $results;
    }
}
?>
