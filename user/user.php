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
}
