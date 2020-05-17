package nl.windesheim.somesite.useractions.admin;

import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;

import static org.assertj.core.api.Assertions.assertThat;

public class SelectUser {
	
	public static Boolean isUserPresent(String username) {
		return Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"user-" + username + "\"]")).size() > 0;
	}
	
	public static void clickOnEditUserButton(String username) {
		Interactions.performClick("//*[@id=\"edituser-" + username + "\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnChangePasswordButton(String username) {
		Interactions.performClick("//*[@id=\"changepassword-" + username + "\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnReset2FAButton(String username) {
		Interactions.performClick("//*[@id=\"reset2fa-" + username + "\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnArchiveUserButton(String username) {
		Interactions.performClick("//*[@id=\"archiveuser-" + username + "\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void clickOnRemoveUserButton(String username) {
		Interactions.performClick("//*[@id=\"deleteuser-" + username + "\"]");
		Webdriver.getInstance().waitForPageLoad();
	}
	
	public static void assertAvailableColumns(Boolean archivedSymbolExpected, Boolean nameExpected, Boolean emailExpected, Boolean roleExpected, Boolean editAccountExpected, Boolean resetPasswordExpected, Boolean resetTotpExpected, Boolean archivedUserExpected, Boolean removeUserExpected) {
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnArchivedUser\"]")).size() > 0).isEqualTo(archivedSymbolExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnUsername\"]")).size() > 0).isTrue();
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnName\"]")).size() > 0).isEqualTo(nameExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnEmail\"]")).size() > 0).isEqualTo(emailExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnRole\"]")).size() > 0).isEqualTo(roleExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnEditAccount\"]")).size() > 0).isEqualTo(editAccountExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnResetPassword\"]")).size() > 0).isEqualTo(resetPasswordExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnResetTOTP\"]")).size() > 0).isEqualTo(resetTotpExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnArchiveAccount\"]")).size() > 0).isEqualTo(archivedUserExpected);
		assertThat(Webdriver.getInstance().getDriver().findElements(By.xpath("//*[@id=\"columnRemoveAccount\"]")).size() > 0).isEqualTo(removeUserExpected);
	}
	
}
