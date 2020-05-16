package nl.windesheim.somesite.useractions.user;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class ArchivedUser {
	
	public static void clickOnBackToIndex() {
		Interactions.performClick("//*[@id=\"backToIndex\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertIsArchived(Boolean expected) {
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"messageAccountArchived\"]")).size() > 0).isEqualTo(expected);
	}
	
}
