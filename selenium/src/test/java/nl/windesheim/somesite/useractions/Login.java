package nl.windesheim.somesite.useractions;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class Login {
	
	public static void fillCredentials(String username, String password) {
		Interactions.fillTextbox("//*[@id=\"username\"]", username);
		Interactions.fillTextbox("//*[@id=\"password\"]", password);
	}
	
	public static void clickOnLoginButton() {
		Interactions.performClick("//*[@id=\"buttonLogin\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertSuccessfulLogin() {
		Boolean loginPresent = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"login\"]")).size() > 0;
		assertThat(loginPresent).isFalse();
		Boolean currentUserPresent = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"currentUser\"]")).size() > 0;
		assertThat(currentUserPresent).isTrue();
	}
}
