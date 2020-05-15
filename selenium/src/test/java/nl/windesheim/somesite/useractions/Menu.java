package nl.windesheim.somesite.useractions;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;

public class Menu {
	public static void clickOnLogin() {
		Interactions.performClick("//*[@id=\"login\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectAccountsAndClickOnMaakNieuwAccountAan() {
		Interactions.performClick("//*[@id=\"createaccount\"]");
		Interactions.performClick("//*[@id=\"logout\"]");
		Webdriver.getInstance().waitForPageLoad();
	}

	public static void selectAccountsAndClickOnBeheerAccounts() {
		Interactions.performClick("//*[@id=\"accountmanagement\"]");
		Interactions.performClick("//*[@id=\"manageaccounts\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectCurrentUserAndClickOnUitloggen() {
		Interactions.performClick("//*[@id=\"currentUser\"]");
		Interactions.performClick("//*[@id=\"logout\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectCurrentUserAndClickOnWijzigWachtwoord() {
		Interactions.performClick("//*[@id=\"currentUser\"]");
		Interactions.performClick("//*[@id=\"changepassword\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectCurrentUserAndClickOnEnable2FA() {
		Interactions.performClick("//*[@id=\"currentUser\"]");
		Interactions.performClick("//*[@id=\"enable2fa\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectCurrentUserAndClickOn2FAVerwijderen() {
		Interactions.performClick("//*[@id=\"currentUser\"]");
		Interactions.performClick("//*[@id=\"disable2fa\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void selectCreateOwnNewAccount() {
		Interactions.performClick("//*[@id=\"createnewaccount\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
}
