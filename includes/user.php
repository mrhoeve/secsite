<?php
include_once(dirname(__FILE__) . "/dbconnection.php");
include_once(dirname(__FILE__) . "/definitions.php");
include_once(dirname(__FILE__) . "/GoogleAuthenticator.php");

class User
{
    private $username;
    private $firstName;
    private $email;
    private $has2fa;
    private $role;
    private $permission = array();
    private $changepwonl;
    private $disabled;
    private $timestamp;

    /**
     * user constructor.
     * @param $username
     * @param $firstName
     * @param $email
     * @param $has2fa
     * @param $role
     * @param array $permission
     * @param $changepwonl
     * @param $disabled
     * @param $timestamp
     */
    public function __construct($username = '', $firstName = '', $email = '', $has2fa = false, $role = '', array $permission = array(), $changepwonl = false, $disabled = false, $timestamp = null)
    {
        $this->username = $username;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->has2fa = $has2fa;
        if (!$role || $role == "none") {
            $role = "Reguliere gebruiker";
        }
        $this->role = $role;
        $this->permission = $permission;
        $this->changepwonl = boolval($changepwonl);
        $this->disabled = boolval($disabled);
        $this->timestamp = $timestamp;

        debugToConsole("User class initialized user: \n" . $this->toString());
    }

    public function get_username()
    {
        return $this->username;
    }

    public function get_firstName()
    {
        return $this->firstName;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function has2fa()
    {
        return $this->has2fa;
    }

    public function set_has2fa($hasIt)
    {
        $this->has2fa = $hasIt;
    }

    public function get_role()
    {
        return $this->role;
    }

    public function get_changepwonl()
    {
        return (int)filter_var($this->changepwonl, FILTER_VALIDATE_BOOLEAN);
    }

    public function mustChangePasswordOnNextLogon()
    {
        return $this->changepwonl;
    }

    public function get_disabled()
    {
        return (int)filter_var($this->disabled, FILTER_VALIDATE_BOOLEAN);
    }

    public function isDisabled()
    {
        return $this->disabled;
    }

    public function get_timestamp()
    {
        return $this->timestamp;
    }

    public function hasPermission($wantedPermission)
    {
        return in_array($wantedPermission, $this->permission);
    }

    public function isEmpty()
    {
        return empty($this->username);
    }

    public function toString()
    {
        $toString = "Username = $this->username \n";
        $toString .= "First name = $this->firstName \n";
        $toString .= "Email = $this->email \n";
        $toString .= "Has 2FA: " . ($this->has2fa ? 'true' : 'false') . "\n";
        $toString .= "Role = $this->role \n";
        $toString .= "Permissions: " . implode(" | ", $this->permission) . "\n";
        $toString .= "Must change password at next logon: " . ($this->mustChangePasswordOnNextLogon() ? 'true' : 'false') . "\n";
        $toString .= "Account is disabled: " . ($this->isDisabled() ? 'true' : 'false') . "\n";
        return $toString . "Timestamp: " . $this->timestamp . "\n";
    }

}

class UserHelper
{

    // Load and authenticate a user

    public static function loadUser($username)
    {
        return self::loadAndAuthenticateUser($username);
    }

    public static function authenticateUserWithoutLoggingIn($username, $password, $facode)
    {
        return self::loadAndAuthenticateUser($username, $password, $facode, true, false);
    }

    public static function authenticateAndLoginUser($username, $password, $facode)
    {
        return self::loadAndAuthenticateUser($username, $password, $facode, true, true);
    }

    private static function loadAndAuthenticateUser($username, $password = "", $facode = "", $performAuthentication = false, $loginInSession = false)
    {
        global $pdoread;

        debugToConsole("Requesting read of username \"" . $username . "\"");

        if ($loginInSession) {
            $_SESSION[SESSION_LOGGEDIN] = FALSE;
        }
        // Check if we have a valid read connection
        if (!isset($pdoread)) {
            die('Failed to setup a database connection');
        }

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.username, u.firstName, u.password, u.fasecret, u.email, u.role, u.changepwonl, u.disabled, GROUP_CONCAT(rp.permission SEPARATOR \',\') as permission, u.timestamp from user u left join rolepermission rp on rp.role=u.role where username = :username')) {
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            if ($stmt->rowCount() === 1) {
                debugToConsole("Match found in database with username \"$username\"");
                $result = $stmt->fetch();
                // Account exists, save it to user $user
                $user = new User($result['username'], $result['firstName'], $result['email'], !empty($result['fasecret']), $result['role'], explode(',', $result['permission']), $result['changepwonl'], $result['disabled'], $result['timestamp']);
                // Do we have to perform the authentication? Then verify the password.
                if ($performAuthentication === true) {
                    $user = self::authenticateUser($user, $password, $result['password'], $facode, $result['fasecret'], $loginInSession);
                }
            } else {
                $user = new User();
            }
        } else {
            die('Internal error setting up the database connection');
        }
        return $user;
    }

    private static function authenticateUser($user, $password, $hashedPassword, $facode, $fasecret, $loginInSession)
    {
        debugToConsole("Authentication requested");
        if (password_verify($password, $hashedPassword) && self::validFaCode($facode, $fasecret)) {
            debugToConsole("Password and 2FA matches with the stored credentials");
            if ($loginInSession) {
                debugToConsole("Logging in in the session, as requested");
                // Authentication success! user has loggedin!
                // Save the user to the session
                $_SESSION[SESSION_LOGGEDIN] = TRUE;
                $_SESSION[SESSION_USER] = serialize($user);
            }
        } else {
            debugToConsole("Password and/or 2FA don't match the stored credentials, destroy the session");
            // Authentication failed, make sure to return an empty user
            $user = new User();
            if ($loginInSession) {
                // If logging in in the session was required, then destroy the session due to failed login attempt
                session_unset();
                session_destroy();
                $_SESSION[SESSION_LOGGEDIN] = FALSE;
            }
        }
        return $user;
    }

    public static function validFaCode($facode, $fasecret)
    {
        if ($fasecret == null) {
            // There's no secret for this user. In that case the code must also be empty for this method to return true
            return empty($facode);
        }
        // Perform a few basic checks, if one of these fail it certainly isn't a valid code
        if (empty($facode) || strlen($facode) != 6 || !is_numeric($facode)) return false;
        $ga = new PHPGangsta_GoogleAuthenticator();
        return $ga->verifyCode($fasecret, $facode, 1);
    }

    // Validate user information

    public static function isUsernameValid($username)
    {
        return !empty($username) && self::isUsernameLongEnough($username) && !self::isUsernameRegistrered($username);
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
        return !empty($emailaddress) && $isEmailValid && !$isRegistered;
    }

    private static function calculatePasswordStrength($password)
    {
        $strengthScore = 0;
        if (preg_match("/[a-zA-Z0-9][a-zA-Z0-9]+/", $password)) {
            $strengthScore += 10;
        }
        if (preg_match("/[~<>?]+/", $password)) {
            $strengthScore += 15;
        }
        if (preg_match("/[!@#$%^&*()]+/", $password)) {
            $strengthScore += 15;
        }
        if (strlen($password) > 8) {
            $lengthScore = (strlen($password) - 8) * 5;
            if ($lengthScore > 60) {
                $lengthScore = 60;
            }
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
        return preg_match('/^\w{5,}$/', $username) === 1;
    }

    private static function isEmailaddressRegistrered($emailaddress)
    {
        global $pdoread;

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.email from user u where email = :email')) {
            $stmt->bindParam(':email', $emailaddress);
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } else {
            die('Internal error setting up the database connection');
        }
    }

    private static function isUsernameRegistrered($username)
    {
        global $pdoread;

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.username from user u where username = :username')) {
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } else {
            die('Internal error setting up the database connection');
        }
    }

    // Save a user
    public static function saveUser(User $user, $password = "", $update = true)
    {
        global $pdosave;
        // Check if we have a valid save connection
        if (!isset($pdosave)) {
            die('Failed to setup a database connection');
        }
        // Check if a password has been provided when a new account is being created
        if (!$update && !$password) {
            die("Not all requirements to save the user information has been met.");
        }
        if ($update) {
            return self::saveExistingUser($user, $password);
        } else {
            return self::saveNewUser($user, $password);
        }
    }

    private static function saveExistingUser(User $user, $password)
    {
        global $pdosave;

        if ($password) {
            $sqlstmt = 'update user set firstName = :firstName, password = :password, email = :email, role = :role, changepwonl = :changepwonl, disabled = :disabled where username = :username';
        } else {
            $sqlstmt = 'update user set firstName = :firstName, email = :email, role = :role, changepwonl = :changepwonl, disabled = :disabled where username = :username';
        }
        // Prepare our SQL
        if ($stmt = $pdosave->prepare($sqlstmt)) {
            // The db fields role and disabled are not being set because we want the defaults for those fields.
            $stmt->bindValue(':username', $user->get_username());
            $stmt->bindValue(':firstName', $user->get_firstName());
            if ($password) {
                $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
            }
            $stmt->bindValue(':email', $user->get_email());
            if($user->get_role()=="Reguliere gebruiker") {
                $stmt->bindValue(':role', null);
            } else {
                $stmt->bindValue(':role', $user->get_role());
            }
            $stmt->bindValue(':changepwonl', $user->get_changepwonl());
            $stmt->bindValue(':disabled', $user->get_disabled());
            $stmt->execute();
            return $user;
        } else {
            return new User();
        }
    }

    private static function saveNewUser(User $user, $password)
    {
        global $pdosave;
        // Prepare our SQL
        if ($stmt = $pdosave->prepare('insert into user (username, firstName, password, email, changepwonl, role, disabled) values (:username, :firstName, :password, :emailaddress, :changepwonl, :role, :disabled)')) {
            $stmt->bindValue(':username', $user->get_username());
            $stmt->bindValue(':firstName', $user->get_firstName());
            $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
            $stmt->bindValue(':emailaddress', $user->get_email());
            $stmt->bindValue(":changepwonl", $user->get_changepwonl());
            if($user->get_role()=="Reguliere gebruiker") {
                $stmt->bindValue(':role', null);
            } else {
                $stmt->bindValue(':role', $user->get_role());
            }
            $stmt->bindValue(":disabled", $user->get_disabled());
            $stmt->execute();
            return $user;
        } else {
            return new User();
        }
    }

    public static function save2FASecret(User $user, $fasecret)
    {
        global $pdosave;
        $isCurrentUser = ($user->get_username() === UserHelper::validateUserAndTimestamp(unserialize($_SESSION['user']))->get_username());
        // Prepare our SQL
        if ($stmt = $pdosave->prepare('update user set fasecret=:fasecret where username = :username')) {
            $stmt->bindValue(':username', $user->get_username());
            $stmt->bindValue(':fasecret', $fasecret);
            $stmt->execute();
            if ($isCurrentUser) {
                // Update the current user (we're working with timestamps for validation, and that's changed)
                $_SESSION[SESSION_USER] = serialize(self::loadUser($user->get_username()));
            }
        } else {
            die('Internal error setting up the database connection');
        }
    }

    public static function validateUserAndTimestamp(User $user)
    {
        global $pdoread;
        $username = $user->get_username();


        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.timestamp from user u where username = :username')) {
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($user->get_timestamp() != $result['timestamp']) {
                debugToConsole("Validating user \"$username\" - Invalid timestamp, clearing user");
                session_unset();
                session_destroy();
                $_SESSION[SESSION_LOGGEDIN] = FALSE;
                $user = new User();
            }
            if (!$user->isEmpty()) {
                debugToConsole("Validating user \"$username\" - OK");
            }
            return $user;
        } else {
            return false;
        }

    }

    public static function loadAllUsers()
    {
        global $pdoread;

        debugToConsole("Requesting list of all Users...");

        // Check if we have a valid read connection
        if (!isset($pdoread)) {
            die('Failed to setup a database connection');
        }

        $listUsers = [];

        // Prepare our SQL
        if ($stmt = $pdoread->prepare('select u.username, u.firstName, u.fasecret, u.email, u.role, u.changepwonl, u.disabled, u.timestamp from user u')) {
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                while ($result = $stmt->fetch()) {
                    // Save account to user and add it to array
                    $user = new User($result['username'], $result['firstName'], $result['email'], !empty($result['fasecret']), $result['role'], array(), $result['changepwonl'], $result['disabled'], $result['timestamp']);
                    array_push($listUsers, serialize($user));
                }
            }
        } else {
            die('Internal error setting up the database connection');
        }
        return $listUsers;
    }

    private static function checkcodeSalt(): string
    {
        return "nNxCMPgg06mZsGwn";
    }

    private
    static function unserializeUserObject(User $user): User
    {
        return $user;
    }

    public
    static function checkCodeAndGetUser($encodedUser, $checkcode): User
    {
        debugToConsole("Checking encoded user\nDecoding user...");
        $userWithSalt = self::checkcodeSalt() . $encodedUser;
        $calculatedCheckcode = md5($userWithSalt);
        if ($calculatedCheckcode != $checkcode) {
            debugToConsole("Calculated checkcode doesn't match given checkcode -> return empty user");
            return new User();
        }
        $serializedUser = base64_decode($encodedUser, true);
        $unserializedUser = self::unserializeUserObject(unserialize($serializedUser));
        debugToConsole("Deserializing user " . $unserializedUser->get_username());
        return $unserializedUser;
    }

    public
    static function calculateCheckcode(string $serializedUser): string
    {
        $userWithSalt = self::checkcodeSalt() . $serializedUser;
        return md5($userWithSalt);
    }

}
