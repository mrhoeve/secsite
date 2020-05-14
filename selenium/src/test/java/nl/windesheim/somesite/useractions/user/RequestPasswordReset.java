package nl.windesheim.somesite.useractions.user;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class RequestPasswordReset {
	
	public static void fillUsername(String username) {
		Interactions.fillTextbox("//*[@id=\"username\"]", username);
	}
	
	public static void clickOnResetPasswordButton() {
		Interactions.performClick("//*[@id=\"buttonResetPassword\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertMessagePresent() {
		Boolean messagePresent = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"message\"]")).size() > 0;
		assertThat(messagePresent).isTrue();
		Boolean buttonPresent = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"buttonBackToIndex\"]")).size() > 0;
		assertThat(buttonPresent).isTrue();
	}
	
	public static void clickOnBackToIndexButton() {
		Interactions.performClick("//*[@id=\"buttonBackToIndex\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
}
