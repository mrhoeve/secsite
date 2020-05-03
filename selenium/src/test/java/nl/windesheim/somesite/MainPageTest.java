package nl.windesheim.somesite;

import nl.windesheim.somesite.database.Database;
import nl.windesheim.somesite.useractions.UserActions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.openqa.selenium.remote.RemoteWebDriver;

@TestInstance(TestInstance.Lifecycle.PER_CLASS)
public class MainPageTest {
	private final String USERNAME = "admin";
	private final String PASSWORD = "WelcomeAdmin01";
	
	private RemoteWebDriver driver;
	
	@BeforeAll
	void setUp() {
		Database.getInstance().resetUserForLogin(USERNAME, PASSWORD);
		driver = Webdriver.getInstance().getDriver();
	}
	
	@AfterAll
	void tearDown() {
		driver.quit();
	}
	
	@Test
	public void openPage() {
		login();
	}
	
	private void login() {
		UserActions.LoginUser(driver, USERNAME, PASSWORD);
	}
}