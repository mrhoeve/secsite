package nl.windesheim.somesite.interactions;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.remote.RemoteWebDriver;

import static org.junit.jupiter.api.Assertions.fail;

public class Interactions {
	public static void fillTextbox(RemoteWebDriver driver, String xPath, String fillWith) {
		try {
			driver.findElement(By.xpath(xPath)).sendKeys(fillWith);
		} catch (NoSuchElementException e) {
			fail();
		}
	}
	
	public static void performClick(RemoteWebDriver driver, String xPath) {
		try {
			driver.findElement(By.xpath(xPath)).click();
		} catch (NoSuchElementException e) {
			fail();
		}
	}
	
}
