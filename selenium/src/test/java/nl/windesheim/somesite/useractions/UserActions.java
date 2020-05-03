package nl.windesheim.somesite.useractions;

import nl.windesheim.somesite.settings.Settings;
import nl.windesheim.somesite.webdriver.Webdriver;

public class UserActions {
	public static void navigateTo(String page) {
		Webdriver.getInstance().getDriver().get(Settings.BASE_URL + page);
		Webdriver.getInstance().waitForPageLoad();
	}
}
