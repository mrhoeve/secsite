package nl.windesheim.somesite;

import nl.windesheim.somesite.database.Database;
import nl.windesheim.somesite.docker.KGenericContainer;
import nl.windesheim.somesite.dto.User;
import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.useractions.Menu;
import nl.windesheim.somesite.useractions.UserActions;
import nl.windesheim.somesite.useractions.user.*;
import org.assertj.core.api.Assertions;
import org.junit.ClassRule;
import org.junit.jupiter.api.*;
import org.testcontainers.containers.wait.strategy.Wait;

import java.time.Duration;
import java.time.temporal.ChronoUnit;

import static nl.windesheim.somesite.useractions.UserActions.navigateTo;

@TestInstance(TestInstance.Lifecycle.PER_CLASS)
@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class UseraccountActionsTest {
	@ClassRule
	public static final KGenericContainer maildevContainer;
	
	private final String maildevURL = "http://localhost:" + maildevContainer.getMappedPort(80);
	
	private final User user=new User("testU");
	
	@BeforeAll
	void setUp() {
		Database.getInstance().setSmtpPort(String.valueOf(maildevContainer.getMappedPort(25)));
		Database.getInstance().deleteUserIfExists(user.getUsername());
	}
	
	@AfterAll
	void tearDown() {
		Database.getInstance().resetSmtpPort();
	}
	
//	@Test
//	@Ignore
//	public void testAlleUserAccountActions() {
//		createUser();
//		login();
//		enable2FATest();
//		changePassword();
//		loguitEnLogin();
//		loguitEnResetLostPasswordWith2FA();
//		remove2FATest();
//		loguitEnResetLostPasswordWithout2FA();
//
//		Database.getInstance().resetSmtpPort();
//	}
	
	@Order(1)
	@Test
	public void createUser() {
		String wrongUsername = "test";
		String wrongEmail = "test@test";
		String rightEmail = "test@test.com";
		String goodNewPassword = "Az09!@#$%&*(<>?)";
		String newPasswordWithError = "Az091@#$%&*(<>?)";
		String notStrongEnoughPassword = "Az!@#$%&*()";
		
		// Test verkeerde username
		navigateTo("index.php");
		Menu.selectCreateOwnNewAccount();
		CreateNewUser.fillUsername(wrongUsername);
		CreateNewUser.fillFirstname("first name");
		CreateNewUser.fillEmail(rightEmail);
		CreateNewUser.fillFirstNewPassword(goodNewPassword);
		CreateNewUser.fillSecondNewPassword(goodNewPassword);
		CreateNewUser.clickOnSubmitButton();
		CreateNewUser.assertError(true);
		CreateNewUser.assertSuccess(false);
		
		// Test verkeerd emailadres
		CreateNewUser.fillUsername(user.getUsername());
		CreateNewUser.fillFirstname("first name");
		CreateNewUser.fillEmail(wrongEmail);
		CreateNewUser.fillFirstNewPassword(goodNewPassword);
		CreateNewUser.fillSecondNewPassword(goodNewPassword);
		CreateNewUser.clickOnSubmitButton();
		CreateNewUser.assertError(true);
		CreateNewUser.assertSuccess(false);
		
		// Test tweemaal verkeerd wachtwoord
		CreateNewUser.fillUsername(user.getUsername());
		CreateNewUser.fillFirstname("first name");
		CreateNewUser.fillEmail(rightEmail);
		CreateNewUser.fillFirstNewPassword(notStrongEnoughPassword);
		CreateNewUser.fillSecondNewPassword(notStrongEnoughPassword);
		CreateNewUser.clickOnSubmitButton();
		CreateNewUser.assertError(true);
		CreateNewUser.assertSuccess(false);
		
		// Test eenmaal juist en eenmaal verkeerd wachtwoord
		CreateNewUser.fillUsername(user.getUsername());
		CreateNewUser.fillFirstname("first name");
		CreateNewUser.fillEmail(rightEmail);
		CreateNewUser.fillFirstNewPassword(goodNewPassword);
		CreateNewUser.fillSecondNewPassword(newPasswordWithError);
		CreateNewUser.clickOnSubmitButton();
		CreateNewUser.assertError(true);
		CreateNewUser.assertSuccess(false);
		
		// Test goede gegevens
		CreateNewUser.fillUsername(user.getUsername());
		CreateNewUser.fillFirstname("first name");
		CreateNewUser.fillEmail(rightEmail);
		CreateNewUser.fillFirstNewPassword(goodNewPassword);
		CreateNewUser.fillSecondNewPassword(goodNewPassword);
		CreateNewUser.clickOnSubmitButton();
		CreateNewUser.assertError(false);
		CreateNewUser.assertSuccess(true);
		CreateNewUser.clickOnBackToIndexButton();
		user.setPassword(goodNewPassword);
	}
	
	@Test
	@Order(2)
	public void login() {
		navigateTo("index.php");
		Menu.clickOnLogin();
		// Foute login 1
		Login.fillCredentials(user.getUsername(), user.getPassword());
		Login.fill2FACode("123456");
		Login.clickOnLoginButton();
		Login.assertError(true);
		
		// Foute login 2
		Login.fillCredentials(user.getUsername(), "userGetPassword()");
		Login.clickOnLoginButton();
		Login.assertError(true);
		
		Login.fillCredentials(user.getUsername(), user.getPassword());
		Login.clickOnLoginButton();
		Login.assertError(false);
		Login.assertSuccessfulLogin();
	}
	
	@Order(3)
	@Test
	public void enable2FATest() {
		String secret;
		
		navigateTo("index.php");
		Menu.selectCurrentUserAndClickOnEnable2FA();
		
		// Eerst, verkeerd wachtwoord met juiste 2FA code
		secret = Enable2FA.currentSecret();
		Enable2FA.fillPassword(user.getPassword() + "1");
		Enable2FA.fill2FACode(Interactions.calculate2FACode(secret));
		Enable2FA.clickOnSubmitButton();
		Enable2FA.assertError(true);
		Enable2FA.assertSuccess(false);
		
		// Daarna, juist wachtwoord met verkeerde 2FA code
		Enable2FA.fillPassword(user.getPassword());
		Enable2FA.fill2FACode("1234567");
		Enable2FA.clickOnSubmitButton();
		Enable2FA.assertError(true);
		Enable2FA.assertSuccess(false);
		
		// Tot slot, met juiste wachtwoord en code
		secret = Enable2FA.currentSecret();
		user.setFaSecret(secret);
		Enable2FA.fillPassword(user.getPassword());
		Enable2FA.fill2FACode(Interactions.calculate2FACode(secret));
		Enable2FA.clickOnSubmitButton();
		Enable2FA.assertError(false);
		Enable2FA.assertSuccess(true);
		Enable2FA.clickOnBackToIndexButton();
	}
	
	@Order(4)
	@Test
	public void changePassword() {
		String goodNewPassword = "Az09!@#$%&*()<>?";
		String newPasswordWithError = "Az091@#$%&*()<>?";
		String notStrongEnoughPassword = "Az!@#$%&*()";
		
		Menu.selectCurrentUserAndClickOnWijzigWachtwoord();
		ChangePassword.assertMustChange(false);
		
		// Geef goed huidig wachtwoord en 2 keer een goed nieuw wachtwoord, maar geen TOTP code
		ChangePassword.fillCurrentPassword(user.getPassword());
		ChangePassword.fillFirstNewPassword(goodNewPassword);
		ChangePassword.fillSecondNewPassword(goodNewPassword);
		ChangePassword.clickOnSubmitButton();
		ChangePassword.assertMustChange(false);
		ChangePassword.assertError(true);
		ChangePassword.assertSuccess(false);
		
		// Geef goed huidig wachtwoord, goede TOTP code en 2 keer een te zwak wachtwoord
		ChangePassword.fillCurrentPassword(user.getPassword());
		ChangePassword.fillFirstNewPassword(notStrongEnoughPassword);
		ChangePassword.fillSecondNewPassword(notStrongEnoughPassword);
		ChangePassword.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		ChangePassword.clickOnSubmitButton();
		ChangePassword.assertMustChange(false);
		ChangePassword.assertError(true);
		ChangePassword.assertSuccess(false);
		
		// Geef goed huidig wachtwoord, goede TOTP code en 2 verschillende wachtwoorden
		ChangePassword.fillCurrentPassword(user.getPassword());
		ChangePassword.fillFirstNewPassword(goodNewPassword);
		ChangePassword.fillSecondNewPassword(newPasswordWithError);
		ChangePassword.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		ChangePassword.clickOnSubmitButton();
		ChangePassword.assertMustChange(false);
		ChangePassword.assertError(true);
		ChangePassword.assertSuccess(false);
		
		// Geef goed huidig wachtwoord, goede TOTP code en 2 keer een juist nieuw wachtwoord
		ChangePassword.fillCurrentPassword(user.getPassword());
		ChangePassword.fillFirstNewPassword(goodNewPassword);
		ChangePassword.fillSecondNewPassword(goodNewPassword);
		ChangePassword.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		ChangePassword.clickOnSubmitButton();
		ChangePassword.assertMustChange(false);
		ChangePassword.assertError(false);
		ChangePassword.assertSuccess(true);
		
		user.setPassword(goodNewPassword);
		
		ChangePassword.clickOnBackToIndexButton();
	}
	
	@Order(5)
	@Test
	public void loguitEnLogin() {
		Menu.selectCurrentUserAndClickOnUitloggen();
		Menu.clickOnLogin();
		Login.fillCredentials(user.getUsername(), user.getPassword());
		Login.clickOnLoginButton();
		Login.assertError(true);
		
		Login.fillCredentials(user.getUsername(), "JustSomePassword@12");
		Login.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		Login.clickOnLoginButton();
		Login.assertError(true);
		
		Login.fillCredentials(user.getUsername(), user.getPassword());
		Login.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		Login.clickOnLoginButton();
		Login.assertError(false);
		Login.assertSuccessfulLogin();
	}
	
	@Order(6)
	@Test
	public void loguitEnResetLostPasswordWith2FA() {
		Interactions.removeAllEmail(maildevURL);
		
		String goodNewPassword = "Az09!@#$%&*()<?>";
		String newPasswordWithError = "Az091@#$%&*()<?>";
		String notStrongEnoughPassword = "Az!@#$%&*()";
		
		String sendCode;
		Menu.selectCurrentUserAndClickOnUitloggen();
		Menu.clickOnLogin();
		Login.clickOnPasswordForgottenButton();
		RequestPasswordReset.fillUsername(user.getUsername() + "fake");
		RequestPasswordReset.clickOnResetPasswordButton();
		RequestPasswordReset.assertMessagePresent();
		RequestPasswordReset.clickOnBackToIndexButton();
		sendCode = Interactions.checkForEmailResetcodeForUser(maildevURL, user.getUsername() + "fake");
		Assertions.assertThat(sendCode).isNull();
		
		Menu.clickOnLogin();
		Login.clickOnPasswordForgottenButton();
		RequestPasswordReset.fillUsername(user.getUsername());
		RequestPasswordReset.clickOnResetPasswordButton();
		RequestPasswordReset.assertMessagePresent();
		sendCode = Interactions.checkForEmailResetcodeForUser(maildevURL, user.getUsername());
		Assertions.assertThat(sendCode).isNotNull();
		
		//
		UserActions.navigateTo("user/resetpassword.php");
		
		// Geef goede resetcode en 2 keer een goed nieuw wachtwoord, maar geen TOTP code
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(goodNewPassword);
		ResetPassword.fillSecondNewPassword(goodNewPassword);
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(true);
		ResetPassword.assertSuccess(false);
		
		// Geef goede resetcode, goede TOTP code en 2 keer een te zwak wachtwoord
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(notStrongEnoughPassword);
		ResetPassword.fillSecondNewPassword(notStrongEnoughPassword);
		ResetPassword.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(true);
		ResetPassword.assertSuccess(false);
		
		// Geef goede resetcode, goede TOTP code en 2 verschillende wachtwoorden
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(goodNewPassword);
		ResetPassword.fillSecondNewPassword(newPasswordWithError);
		ResetPassword.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(true);
		ResetPassword.assertSuccess(false);
		
		// Geef goede resetcode, goede TOTP code en 2 keer een juist nieuw wachtwoord
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(goodNewPassword);
		ResetPassword.fillSecondNewPassword(goodNewPassword);
		ResetPassword.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(false);
		ResetPassword.assertSuccess(true);
		
		user.setPassword(goodNewPassword);
	}
	
	@Order(7)
	@Test
	public void remove2FATest() {
		String passwordWithError = "Az091@#$%&*()<>?";
		
		Menu.selectCurrentUserAndClickOn2FAVerwijderen();
		
		// Verkeerd wachtwoord met juiste TOTP code
		Remove2FA.fillPassword(passwordWithError);
		Remove2FA.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		Remove2FA.clickOnSubmitButton();
		Remove2FA.assertError(true);
		Remove2FA.assertSuccess(false);

		// Juist wachtwoord met verkeerde TOTP code
		Remove2FA.fillPassword(user.getPassword());
		Remove2FA.fill2FACode("1234567");
		Remove2FA.clickOnSubmitButton();
		Remove2FA.assertError(true);
		Remove2FA.assertSuccess(false);
		
		// Juist wachtwoord met juiste TOTP code
		Remove2FA.fillPassword(user.getPassword());
		Remove2FA.fill2FACode(Interactions.calculate2FACode(user.getFaSecret()));
		Remove2FA.clickOnSubmitButton();
		Remove2FA.assertError(false);
		Remove2FA.assertSuccess(true);
		user.setFaSecret("");
		Remove2FA.clickOnBackToIndexButton();
	}
	
	@Order(8)
	@Test
	public void loguitEnResetLostPasswordWithout2FA() {
		Interactions.removeAllEmail(maildevURL);
		
		String goodNewPassword = "Az09!@#$%&*(<?>)";
		String newPasswordWithError = "Az091@#$%&*(<?>)";
		String notStrongEnoughPassword = "Az!@#$%&*()";
		
		String sendCode;
		Menu.selectCurrentUserAndClickOnUitloggen();
		Menu.clickOnLogin();
		Login.clickOnPasswordForgottenButton();
		RequestPasswordReset.fillUsername(user.getUsername() + "fake");
		RequestPasswordReset.clickOnResetPasswordButton();
		RequestPasswordReset.assertMessagePresent();
		RequestPasswordReset.clickOnBackToIndexButton();
		sendCode = Interactions.checkForEmailResetcodeForUser(maildevURL, user.getUsername() + "fake");
		Assertions.assertThat(sendCode).isNull();
		
		Menu.clickOnLogin();
		Login.clickOnPasswordForgottenButton();
		RequestPasswordReset.fillUsername(user.getUsername());
		RequestPasswordReset.clickOnResetPasswordButton();
		RequestPasswordReset.assertMessagePresent();
		sendCode = Interactions.checkForEmailResetcodeForUser(maildevURL, user.getUsername());
		Assertions.assertThat(sendCode).isNotNull();
		
		//
		UserActions.navigateTo("user/resetpassword.php");
		
		// Geef goede resetcode en 2 keer een te zwak wachtwoord
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(notStrongEnoughPassword);
		ResetPassword.fillSecondNewPassword(notStrongEnoughPassword);
		ResetPassword.fill2FACode("");
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(true);
		ResetPassword.assertSuccess(false);
		
		// Geef goede resetcode en 2 verschillende wachtwoorden
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(goodNewPassword);
		ResetPassword.fillSecondNewPassword(newPasswordWithError);
		ResetPassword.fill2FACode("");
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(true);
		ResetPassword.assertSuccess(false);
		
		// Geef goede resetcode en 2 keer een juist nieuw wachtwoord
		ResetPassword.fillUsername(user.getUsername());
		ResetPassword.fillResetCode(sendCode);
		ResetPassword.fillFirstNewPassword(goodNewPassword);
		ResetPassword.fillSecondNewPassword(goodNewPassword);
		ResetPassword.fill2FACode("");
		ResetPassword.clickOnSubmitButton();
		ResetPassword.assertError(false);
		ResetPassword.assertSuccess(true);
		
		user.setPassword(goodNewPassword);
	}
	
	static {
		maildevContainer =
				new KGenericContainer("maildev/maildev")
				.withExposedPorts(80, 25)
				.waitingFor(Wait.forHttp("/email").forPort(80))
				.withStartupTimeout(Duration.of(60, ChronoUnit.SECONDS));
		
		maildevContainer.start();
	}
}