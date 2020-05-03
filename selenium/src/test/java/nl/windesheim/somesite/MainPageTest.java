package nl.windesheim.somesite;

import nl.windesheim.somesite.database.Database;
import nl.windesheim.somesite.dto.User;
import nl.windesheim.somesite.useractions.Enable2FA;
import nl.windesheim.somesite.useractions.Login;
import nl.windesheim.somesite.useractions.Menu;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.openqa.selenium.remote.RemoteWebDriver;

import static nl.windesheim.somesite.useractions.UserActions.navigateTo;

@TestInstance(TestInstance.Lifecycle.PER_CLASS)
public class MainPageTest {
	private final String USERNAME = "admin";
	private final String PASSWORD = "WelcomeAdmin01";
	
	private User user;
	
	private RemoteWebDriver driver;
	
	@BeforeAll
	void setUp() {
		Database.getInstance().resetUserForLogin(USERNAME, PASSWORD);
		user = new User(USERNAME, PASSWORD);
		driver = Webdriver.getInstance().getDriver();
	}
	
	@AfterAll
	void tearDown() {
		driver.quit();
	}
	
	@Test
	public void SuccesvolLogin_Activeer2FA_Test() {
		login();
		enable2FATest();
		System.out.println(user);
	}
	
	private void login() {
		navigateTo("index.php");
		Menu.clickOnLogin();
		Login.fillCredentials(user.getUsername(), user.getPassword());
		Login.clickOnLoginButton();
		Login.assertSuccessfulLogin();
	}
	
	private void enable2FATest() {
		String secret;
		
		navigateTo("index.php");
		Menu.selectCurrentUserAndClickOnEnable2FA();
		
		// Eerst, verkeerd wachtwoord met juiste 2FA code
		secret = Enable2FA.currentSecret();
		Enable2FA.fillPassword(user.getPassword() + "1");
		Enable2FA.fill2FAUsingValidSecret(secret);
		Enable2FA.clickOnSubmitButton();
		Enable2FA.assertError(true);
		Enable2FA.assertSuccess(false);
		
		// Daarna, juist wachtwoord met verkeerde 2FA code
		Enable2FA.fillPassword(user.getPassword());
		Enable2FA.fill2FAUsingInvalidString("1234567");
		Enable2FA.clickOnSubmitButton();
		Enable2FA.assertError(true);
		Enable2FA.assertSuccess(false);
		
		// Tot slot, met juiste wachtwoord en code
		secret = Enable2FA.currentSecret();
		Enable2FA.fillPassword(user.getPassword());
		Enable2FA.fill2FAUsingValidSecret(secret);
		Enable2FA.clickOnSubmitButton();
		Enable2FA.assertError(false);
		Enable2FA.assertSuccess(true);
		Enable2FA.clickOnBackToIndexButton();
	}
}