package nl.windesheim.somesite.useractions.admin;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class AdminCreateNewUser {
	public static void fillUsername(String username) {
		Interactions.fillTextbox("//*[@id=\"username\"]", username);
	}
	
	public static void fillFirstname(String firstname) {
		Interactions.fillTextbox("//*[@id=\"firstname\"]", firstname);
	}
	
	public static void fillEmail(String emailaddress) {
		Interactions.fillTextbox("//*[@id=\"emailaddress\"]", emailaddress);
	}
	
	public static void fillNewPassword(String password) {
		Interactions.fillTextbox("//*[@id=\"password\"]", password);
	}
	
	public static void clickOnSubmitButton() {
		Interactions.performClick("//*[@id=\"submit\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnBackToIndexButton() {
		Interactions.performClick("//*[@id=\"successbutton\"]");
		Webdriver.getInstance().waitForPageLoad();
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
