package nl.windesheim.somesite.useractions;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class Enable2FA {
	public static String currentSecret() {
		return Interactions.getTextFromElement("//*[@id=\"secret\"]");
	}
	
	public static void clickOnSubmitButton() {
		Interactions.performClick("//*[@id=\"submit2fa\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnBackToIndexButton() {
		Interactions.performClick("//*[@id=\"succes2fabutton\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void fillPassword(String password) {
		Interactions.fillTextbox("//*[@id=\"password\"]", password);
	}
	
	public static void fill2FAUsingValidSecret(String secret) {
		Interactions.fill2FACode("//*[@id=\"2facode\"]", secret);
	}
	
	public static void fill2FAUsingInvalidString(String fakecode) {
		Interactions.fillTextbox("//*[@id=\"2facode\"]", fakecode);
	}
	
	public static void assertError(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"error2fa\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
	}
	
	public static void assertSuccess(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"succes2fa\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
		bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"succes2fabutton\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
		
	}
}
