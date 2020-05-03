package org.example.untitled;

import org.example.untitled.database.Database;
import org.example.untitled.webdriver.Webdriver;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.TestInstance;
import org.openqa.selenium.remote.RemoteWebDriver;

import static org.example.untitled.useractions.UserActions.LoginUser;

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
		LoginUser(driver, USERNAME, PASSWORD);
	}
}