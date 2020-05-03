package org.example.untitled;

import org.example.untitled.webdriver.Webdriver;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.chrome.ChromeDriver;

import static org.example.untitled.useractions.UserActions.LoginUser;

public class MainPageTest {
	@Test
	public void openPage() {
		ChromeDriver driver = Webdriver.getInstance().getDriver();
		LoginUser("admin", "WelcomeAdmin01");
		driver.quit();
	}
}