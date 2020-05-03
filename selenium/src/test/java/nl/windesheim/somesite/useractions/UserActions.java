package nl.windesheim.somesite.useractions;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.settings.Settings;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;
import org.openqa.selenium.remote.RemoteWebDriver;

import static org.assertj.core.api.Assertions.assertThat;

public class UserActions {
	public static void LoginUser(RemoteWebDriver driver, String username, String password) {
		driver.get(Settings.BASE_URL + "index.php");
		Webdriver.getInstance().waitForPageLoad();
		Interactions.performClick(driver, "//*[@id=\"login\"]");
		Webdriver.getInstance().waitForPageLoad();
		Interactions.fillTextbox(driver, "//*[@id=\"username\"]", username);
		Interactions.fillTextbox(driver, "//*[@id=\"password\"]", password);
		Interactions.performClick(driver, "//*[@id=\"buttonLogin\"]");
		Webdriver.getInstance().waitForPageLoad();
		
		Boolean loginPresent = driver.findElements(By.xpath("//*[@id=\"login\"]")).size() > 0;
		assertThat(loginPresent).isFalse();
		Boolean currentUserPresent = driver.findElements(By.xpath("//*[@id=\"currentUser\"]")).size() > 0;
		assertThat(currentUserPresent).isTrue();
	}
	
}
