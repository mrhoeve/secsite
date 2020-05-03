package org.example.untitled.useractions;

import org.example.untitled.settings.Settings;
import org.example.untitled.webdriver.Webdriver;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;

import static org.junit.jupiter.api.Assertions.assertEquals;

public class UserActions {
	public static void LoginUser(String username, String password) {
		ChromeDriver driver = Webdriver.getInstance().getDriver();
		driver.get(Settings.BASE_URL + "index.php");
		Webdriver.getInstance().waitForPageLoad();
		WebElement menuLogin = driver.findElement(By.xpath("//*[@id=\"login\"]"));
		menuLogin.click();
		Webdriver.getInstance().waitForPageLoad();
		WebElement inputUsername = driver.findElement(By.xpath("//*[@id=\"username\"]"));
		inputUsername.sendKeys(username);
		WebElement inputPassword = driver.findElement(By.xpath("//*[@id=\"password\"]"));
		inputPassword.sendKeys(password);
		WebElement buttonLogin = driver.findElement(By.xpath("//*[@id=\"buttonLogin\"]"));
		buttonLogin.click();
		Webdriver.getInstance().waitForPageLoad();
		
//		assertEquals(0,driver.findElement(By.xpath("//*[@id=\"login\"]")).getSize());
//		assertEquals(1, driver.findElement(By.xpath("//*[@id=\"currentUser\"]")).getSize());
	}
}
