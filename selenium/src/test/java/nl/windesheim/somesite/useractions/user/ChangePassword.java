package nl.windesheim.somesite.useractions.user;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class ChangePassword {
	public static void fillCurrentPassword(String password) {
		Interactions.fillTextbox("//*[@id=\"currentpassword\"]", password);
	}
	
	public static void fillFirstNewPassword(String password) {
		Interactions.fillTextbox("//*[@id=\"password\"]", password);
	}
	
	public static void fillSecondNewPassword(String password) {
		Interactions.fillTextbox("//*[@id=\"confirmpassword\"]", password);
	}
	
	public static void fill2FACode(String secret) {
		Interactions.fillTextbox("//*[@id=\"2facode\"]", secret);
	}
	
	public static void clickOnSubmitButton() {
		Interactions.performClick("//*[@id=\"submit\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnBackToIndexButton() {
		Interactions.performClick("//*[@id=\"successbutton\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertMustChange(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"mustchangenotification\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
	}
	
	public static void assertError(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"error\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
	}
	
	public static void assertSuccess(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"success\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
		bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"successbutton\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
		
	}
	
}
