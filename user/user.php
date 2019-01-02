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

}

class userHelper
{

    public static function loadUser($userid, $password)
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
                // Account exists, now we verify the password.
                if (password_verify($password, $result['password'])) {
                    $user = new user($result['userid'], $result['firstName'], $result['email'], $result['role'], explode(',', $result['permission']), $result['disabled']);
                    // Verification success! user has loggedin!
                    $_SESSION['loggedin'] = TRUE;
                    $_SESSION['user'] = serialize($user);
                } else {
                    $user = new user('','','',[],array(), '');
                }
            } else {
                $user = new user('','','',[],array(), '');
            }
        } else {
            die('Internal error setting up the database connection');
        }
        return $user;
    }

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
}
