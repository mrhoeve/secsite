package nl.windesheim.somesite.useractions.admin;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class ArchiveUser {
	
	public static void setChangeArchivedAccount(Boolean desiredState) {
		Interactions.setCheckboxToState("//*[@id=\"archivedAccount\"]", desiredState);
	}
	
	public static Boolean getCurrentStateOfArchivedAccount() {
		return Interactions.getCurrentStateOfCheckbox("//*[@id=\"archivedAccount\"]");
	}
	
	public static void clickOnSave() {
		Interactions.performClick("//*[@id=\"saveuser\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnBackToSelect() {
		Interactions.performClick("//*[@id=\"backToSelect\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertSuccess(Boolean expected) {
		Boolean bvalue = Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"success\"]")).size() > 0;
		assertThat(bvalue).isEqualTo(expected);
	}
	
}
