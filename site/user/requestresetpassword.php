<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require(dirname(__FILE__) . '/../includes/PHPMailer/Exception.php');
require(dirname(__FILE__) . '/../includes/PHPMailer/PHPMailer.php');
require(dirname(__FILE__) . '/../includes/PHPMailer/SMTP.php');

include_once(dirname(__FILE__) . "/../includes/definitions.php");
use Psr\Log\LogLevel;
include_once(dirname(__FILE__) . "/../includes/user.php");
include_once(dirname(__FILE__) . "/../includes/setting.php");

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE) {
    header('Location: ..\index.php');
} else {
    // Start with a clean sheet
    $freshStart = true;
    $error = false;

    $user = new User();
    if (isset($_POST['username'])) {
        $freshStart = false;

        $smtpPortSetting = getSettingValue("smtp_port");
        $smtpPort = empty($smtpPortSetting) ? 1025 : (int) $smtpPortSetting;
        $log->log(LogLevel::INFO, "Using SMTP port " . $smtpPort);

        $user = UserHelper::loadUser($_POST['username']);
        if(!$user->isEmpty()) {
            $checkcode = UserHelper::calculateCheckcode(serialize($user));
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host = 'localhost';                    // Set the SMTP server to send through
                $mail->SMTPAuth = false;                                   // Enable SMTP authentication
                $mail->SMTPAutoTLS = false;
                $mail->Port = $smtpPort;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom('noreply@somesite.local', 'Mailer');
                $mail->addAddress($user->get_email(), $user->get_firstName());     // Add a recipient

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Wachtwoord herstel aangevraagd';
                $mail->Body = "Wachtwoord reset link: <a href=\"http://localhost:63312/secsite/security/site/user/resetpassword.php?user={$user->get_username()}&code={$checkcode}\">Reset password</a>";
                $mail->AltBody = "Ga naar http://localhost/secsite/security/site/user/resetpassword.php, vul uw gebruikersnaam in en gebruik de herstelcode {$checkcode}";

                $mail->send();
                $log->log(LogLevel::NOTICE, "Password reset mail sent for user " . $user->get_username());
            } catch (Exception $e) {
                $log->log(LogLevel::CRITICAL, "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                $error = true;
            }
        }
    }

    setLevelToRoot("..");
    include_once(dirname(__FILE__) . "/../includes/header.php");

    ?>
    <section id="resetpassword">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Wachtwoord vergeten</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!$freshStart) { ?>
                                <p id="message">Als uw gebruikersnaam bekend is ontvangt u een mail op het emailadres dat daarbij hoort.</p>
                                <a href="<?php echo LEVEL ?>index.php" class="btn btn-success btn-block mt-2" id="buttonBackToIndex">Terug naar
                                    index</a>
                            <?php } else {
                                // We have a fresh start, or we've got an error
                                if ($error) { ?>
                                    <p class="text-danger" id="error">Gebruikersnaam, wachtwoord of code onjuist.</p>
                                <?php } ?>
                                <form action="requestresetpassword.php" method="post">
                                    <div class="alert alert-light">
                                        Geef uw gebruikersnaam in en klik op 'Reset wachtwoord'.
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Gebruikersnaam</label>
                                        <input type="text" name="username" id="username" placeholder="Gebruikersnaam"
                                               class="form-control">
                                    </div>
                                    <input type="submit" value="Reset wachtwoord" class="btn btn-primary btn-block" id="buttonResetPassword">
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
}
include_once(dirname(__FILE__) . "/../includes/footer.php");
?>
