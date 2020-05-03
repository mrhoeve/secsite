package nl.windesheim.somesite.useractions;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;

public class Menu {
	public static void clickOnLogin() {
		Interactions.performClick("//*[@id=\"login\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectCurrentUserAndClickOnEnable2FA() {
		Interactions.performClick("//*[@id=\"currentUser\"]");
		Interactions.performClick("//*[@id=\"enable2fa\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
}
