package nl.windesheim.somesite;

import com.google.gson.Gson;
import nl.windesheim.somesite.database.Database;
import nl.windesheim.somesite.dto.User;
import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.maildev.MaildevDto;
import nl.windesheim.somesite.useractions.*;
import nl.windesheim.somesite.useractions.user.*;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.openqa.selenium.remote.RemoteWebDriver;

import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;

import static nl.windesheim.somesite.useractions.UserActions.navigateTo;

@TestInstance(TestInstance.Lifecycle.PER_CLASS)
public class LoginLogout2FATest {
	private final String USERNAME = "testU";
	
	private User user;
	
	private RemoteWebDriver driver;
	
	@BeforeAll
	void setUp() {
		Database.getInstance().deleteUserIfExists(USERNAME);
		user = new User(USERNAME);
		driver = Webdriver.getInstance().getDriver();
	}
	
	@AfterAll
	void tearDown() {
		driver.quit();
	}
	
	@Test
	public void SuccesvolLogin_Activeer2FA_Test() {
		createUser();
		login();
		enable2FATest();
		changePassword();
		loguitEnLogin();
		remove2FATest();
	}
	
	public void createUser() {
		String wrongUsername = "test";
		String wrongEmail = "test@test";
		String rightEmail = "test@test.com";
		String goodNewPassword = "Az09!@#$%^&*(~<>?)";
		String newPasswordWithError = "Az091@#$%^&*(~<>?)";
		String notStrongEnoughPassword = "Az!@#$%^&*()";
		
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
	
	private void login() {
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
	
	private void enable2FATest() {
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
	
	private void changePassword() {
		String goodNewPassword = "Az09!@#$%^&*()~<>?";
		String newPasswordWithError = "Az091@#$%^&*()~<>?";
		String notStrongEnoughPassword = "Az!@#$%^&*()";
		
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
	
	private void loguitEnLogin() {
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
	
	
	private void remove2FATest() {
		String passwordWithError = "Az091@#$%^&*()~<>?";
		
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
	
	@Test
	public void Test() {
		String pattern = "^Wachtwoord reset link: <a href=\\\"http:\\/\\/.*\\/resetpassword\\.php\\?user=admin&code=[a-zA-Z0-9]{32}\\\">Reset password<\\/a>.*$";
		try {
		URL url = new URL("http://localhost:1080/email");
		try (InputStreamReader reader = new InputStreamReader(url.openStream())) {
			MaildevDto[] dto = new Gson().fromJson(reader, MaildevDto[].class);
			String user = dto[0].getHtml().split("user=")[1].split("&code=")[0];
			String code = dto[0].getHtml().split("&code=")[1].split("\"")[0];
			dto[0].getHtml().matches(pattern);
			System.out.println(dto.length);
		} catch (IOException e) {
			e.printStackTrace();
		}
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}
	}
}