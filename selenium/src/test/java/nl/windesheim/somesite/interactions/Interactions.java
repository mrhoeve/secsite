package nl.windesheim.somesite.interactions;

import dev.samstevens.totp.code.DefaultCodeGenerator;
import dev.samstevens.totp.code.HashingAlgorithm;
import dev.samstevens.totp.exceptions.CodeGenerationException;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;

import static org.junit.jupiter.api.Assertions.fail;

public class Interactions {
	public static void fillTextbox(String xPath, String fillWith) {
		try {
			Webdriver.getInstance().getDriver().findElement(By.xpath(xPath)).sendKeys(fillWith);
		} catch (NoSuchElementException e) {
			fail();
		}
	}
	
	public static void performClick(String xPath) {
		try {
			Webdriver.getInstance().getDriver().findElement(By.xpath(xPath)).click();
		} catch (NoSuchElementException e) {
			fail();
		}
	}
	
	public static String getTextFromElement(String xPath) {
		try {
			return Webdriver.getInstance().getDriver().findElement(By.xpath(xPath)).getText();
		} catch (NoSuchElementException e) {
			fail();
			return "";
		}
	}
	
	public static void fill2FACode(String xPath, String faSecret) {
		try {
			long currentBucket = Math.floorDiv((System.currentTimeMillis() / 1000L), 30);
			DefaultCodeGenerator g = new DefaultCodeGenerator(HashingAlgorithm.SHA1);
			Interactions.fillTextbox(xPath, g.generate(faSecret, currentBucket));
		} catch (CodeGenerationException e) {
			fail();
		}
	}
}
