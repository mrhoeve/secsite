package org.example.untitled.useractions;

import org.example.untitled.settings.Settings;
import org.example.untitled.webdriver.Webdriver;
import org.openqa.selenium.By;
import org.openqa.selenium.remote.RemoteWebDriver;

import static org.assertj.core.api.Assertions.assertThat;
import static org.example.untitled.interactions.Interactions.fillTextbox;
import static org.example.untitled.interactions.Interactions.performClick;

public class UserActions {
	public static void LoginUser(RemoteWebDriver driver, String username, String password) {
		driver.get(Settings.BASE_URL + "index.php");
		Webdriver.getInstance().waitForPageLoad();
		performClick(driver, "//*[@id=\"login\"]");
		Webdriver.getInstance().waitForPageLoad();
		fillTextbox(driver, "//*[@id=\"username\"]", username);
		fillTextbox(driver, "//*[@id=\"password\"]", password);
		performClick(driver, "//*[@id=\"buttonLogin\"]");
		Webdriver.getInstance().waitForPageLoad();
		
		Boolean loginPresent = driver.findElements(By.xpath("//*[@id=\"login\"]")).size() > 0;
		assertThat(loginPresent).isFalse();
		Boolean currentUserPresent = driver.findElements(By.xpath("//*[@id=\"currentUser\"]")).size() > 0;
		assertThat(currentUserPresent).isTrue();
	}
	
}
