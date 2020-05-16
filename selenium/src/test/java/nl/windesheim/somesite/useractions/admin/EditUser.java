package nl.windesheim.somesite.useractions.admin;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class EditUser {
	
	public static void setChangePwONL(Boolean desiredState) {
		Interactions.setCheckboxToState("//*[@id=\"changepwonl\"]", desiredState);
	}
	
	public static Boolean getCurrentStateOfChangePwONL() {
		return Interactions.getCurrentStateOfCheckbox("//*[@id=\"changepwonl\"]");
	}
	
	public static void clickOnSave() {
		Interactions.performClick("//*[@id=\"saveuser\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertSuccess(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"success\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
		bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"successbutton\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
		
	}
	
	
}
