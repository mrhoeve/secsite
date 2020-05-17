package nl.windesheim.somesite.useractions.admin;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class ResetTOTPSecret {
	
	public static void clickOnRemove2FA() {
		Interactions.performClick("//*[@id=\"confirmremoval2fa\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnBackToSelect() {
		Interactions.performClick("//*[@id=\"successbutton\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertSuccess(Boolean expected) {
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"success\"]")).size() > 0).isEqualTo(expected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"successbutton\"]")).size() > 0).isEqualTo(expected);
	}
}
