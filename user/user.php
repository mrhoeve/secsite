<?php
include_once "../includes/dbconnection.php";


class user
{
    private $userid;
    private $firstName;
    private $email;
    private $role;
    private $permission = array();
    private $disabled;

    /**
     * user constructor.
     * @param $userid
     * @param $firstName
     * @param $email
     * @param $role
     * @param array $permission
     * @param $disabled
     */
    public function __construct($userid, $firstName, $email, $role, array $permission, $disabled)
    {
        $this->userid = $userid;
        $this->firstName = $firstName;
        $this->email = $email;
        if(!$role)
        {
            $role="Normal user";
        }
        $this->role = $role;
        $this->permission = $permission;
        $this->disabled = $disabled;
    }

    public function get_userid()
    {
        return $this->userid;
    }

    public function get_firstName()
    {
        return $this->firstName;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_role()
    {
        return $this->role;
    }

    public function get_disabled()
    {
        return $this->disabled;
    }

    public function hasPermission($wantedPermission)
    {
        return in_array($wantedPermission, $this->permission);
    }

}

class userHelper
{

    // Load and authenticate a user

    public static function loadUser($userid)
    {
        return self::loadAndAuthenticateUser($userid);
    }

    public static function authenticateUser($userid, $password)
    {
        return self::loadAndAuthenticateUser($userid, $password, true);
    }

    private static function loadAndAuthenticateUser($userid, $password = "", $performAuthentication = false)
    {
        global $pdoread;

        $_SESSION['loggedin'] = FALSE;
        // Check if we have a valid read connection
        if (!isset($pdoread)) {
            die('Failed to setup a database connection');
        }

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.userid, u.firstName, u.password, u.email, u.role, u.disabled, GROUP_CONCAT(rp.permission SEPARATOR \',\') as permission from user u join rolepermission rp on rp.role=u.role where userid = :userid')) {
            $stmt->bindParam(':userid', $userid);
            $stmt->execute();
            if ($stmt->rowCount() === 1) {
                $result = $stmt->fetch();
                // Account exists, save it to user $user
                $user = new user($result['userid'], $result['firstName'], $result['email'], $result['role'], explode(',', $result['permission']), $result['disabled']);
                // Do we have to perform the authentication? Then verify the password.
                if($performAuthentication===true) {
                    if (password_verify($password, $result['password'])) {
                        // Authentication success! user has loggedin!
                        // Save the user to the session
                        $_SESSION['loggedin'] = TRUE;
                        $_SESSION['user'] = serialize($user);
                    } else {
                        // Authentication failed, make sure to return an empty user en clear the session
                        $user = new user('', '', '', [], array(), '');
                        session_destroy();
                        $_SESSION['loggedin'] = FALSE;
                        $_SESSION['user'] = serialize($user);
                    }
                }
            } else {
                $user = new user('','','',[],array(), '');
            }
        } else {
            die('Internal error setting up the database connection');
        }
        return $user;
    }

    // Validate user information

    public static function isUsernameValid($username)
    {
        return self::isUsernameLongEnough($username) && !self::isUsernameRegistrered($username);
    }

    public static function arePasswordsValid($passone, $passtwo)
    {
        return self::arePasswordsSimilar($passone, $passtwo) && self::calculatePasswordStrength($passone) > 70;
    }

    public static function isEmailAddressValid($emailaddress)
    {
        // Origin of first function: https://stackoverflow.com/questions/5855811/how-to-validate-an-email-in-php
        $isEmailValid = filter_var($emailaddress, FILTER_VALIDATE_EMAIL) !== false;
        $isRegistered = self::isEmailaddressRegistrered($emailaddress);
        return $isEmailValid && !$isRegistered;
    }

    private static function calculatePasswordStrength($password)
    {
        $strengthScore = 0;
        if(preg_match("/[a-zA-Z0-9][a-zA-Z0-9]+/", $password))
        {
            $strengthScore += 10;
        }
        if(preg_match("/[~<>?]+/", $password))
        {
            $strengthScore += 15;
        }
        if(preg_match("/[!@#$%^&*()]+/", $password))
        {
            $strengthScore += 15;
        }
        if(strlen($password) > 8)
        {
            $lengthScore = (strlen($password) - 8) * 5;
            if($lengthScore > 60) { $lengthScore = 60; }
            $strengthScore += $lengthScore;
        }
        return $strengthScore;
    }

    private static function arePasswordsSimilar($passone, $passtwo)
    {
        return $passone === $passtwo;
    }

    private static function isUsernameLongEnough($username)
    {
        // Origin regex: https://stackoverflow.com/questions/4383878/php-username-validation
        return preg_match('/^\w{5,}$/', $username) === 1 ? true : false;
    }

    private static function isEmailaddressRegistrered($emailaddress)
    {
        global $pdoread;

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.email from user u where email = :email')) {
            $stmt->bindParam(':email', $emailaddress);
            $stmt->execute();
            return $stmt->rowCount() === 1 ? true : false;
        } else {
            die('Internal error setting up the database connection');
        }
    }

    private static function isUsernameRegistrered($username)
    {
        global $pdoread;

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.userid from user u where userid = :userid')) {
            $stmt->bindParam(':userid', $username);
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } else {
            die('Internal error setting up the database connection');
        }
    }

    // Save a user
    public static function saveUser($user, $password="", $update=true)
    {
        global $pdosave;
        if($user instanceof user)
        {
            // Check if we have a valid save connection
            if (!isset($pdosave)) {
                die('Failed to setup a database connection');
            }
            // Check if a password has been provided when a new account is being created
            if(!$update && !$password)
            {
                die("Not all requirements to save the user information has been met.");
            }
            if($update)
            {
                if($password)
                {
                    $sqlstmt = 'update user set firstName = :firstName, password = :password, email = :email, role = :role, disabled = :disabled where userid = :userid';
                } else {
                    $sqlstmt = 'update user set firstName = :firstName, email = :email, role = :role, disabled = :disabled where userid = :userid';
                }
                // Prepare our SQL
                if ($stmt = $pdosave->prepare($sqlstmt)) {
                    // The db fields role and disabled are not being set because we want the defaults for those fields.
                    $stmt->bindValue(':userid', $user->get_userid());
                    $stmt->bindValue(':firstName', $user->get_firstName());
                    if($password)
                    {
                        $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
                    }
                    $stmt->bindValue(':emailaddress', $user->get_email());
                    $stmt->bindValue(':role', $user->get_role());
                    $stmt->bindValue(':disabled', $user->get_disabled());
                    $stmt->execute();
                } else {
                    die('Internal error setting up the database connection');
                }
            } else {
                // Prepare our SQL
                if ($stmt = $pdosave->prepare('insert into user (userid, firstName, password, email) values (:userid, :firstName, :password, :emailaddress)')) {
                    // The db fields role and disabled are not being set because we want the defaults for those fields.
                    $stmt->bindValue(':userid', $user->get_userid());
                    $stmt->bindValue(':firstName', $user->get_firstName());
                    $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
                    $stmt->bindValue(':emailaddress', $user->get_email());
                    $stmt->execute();
                } else {
                    die('Internal error setting up the database connection');
                }
            }
        }
    }
}
