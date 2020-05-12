package nl.windesheim.somesite.interactions;

import com.google.gson.Gson;
import dev.samstevens.totp.code.DefaultCodeGenerator;
import dev.samstevens.totp.code.HashingAlgorithm;
import dev.samstevens.totp.exceptions.CodeGenerationException;
import nl.windesheim.somesite.maildev.MaildevDto;
import nl.windesheim.somesite.webdriver.Webdriver;
import org.assertj.core.api.Assertions;
import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;

import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;

import static org.junit.jupiter.api.Assertions.fail;

public class Interactions {
	public static void fillTextbox(String xPath, String fillWith) {
		try {
			Webdriver.getInstance().getDriver().findElement(By.xpath(xPath)).sendKeys(Keys.chord(Keys.CONTROL, "a"), fillWith);
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
	
	public static String calculate2FACode(String faSecret) {
		try {
			long currentBucket = Math.floorDiv((System.currentTimeMillis() / 1000L), 30);
			DefaultCodeGenerator g = new DefaultCodeGenerator(HashingAlgorithm.SHA1);
			return g.generate(faSecret, currentBucket);
		} catch (CodeGenerationException e) {
			fail();
			return null;
		}
	}
	
	public String checkForEmailResetcodeForUser(String checkForUser) {
		try {
			URL url = new URL("http://localhost:1080/email");
			try (InputStreamReader reader = new InputStreamReader(url.openStream())) {
				MaildevDto[] dto = new Gson().fromJson(reader, MaildevDto[].class);
				String user = dto[0].getHtml().split("user=")[1].split("&code=")[0];
				String code = dto[0].getHtml().split("&code=")[1].split("\"")[0];
				Assertions.assertThat(user).isEqualTo(checkForUser);
				return code;
			} catch (IOException e) {
				e.printStackTrace();
			}
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}
		fail();
		return null;
	}
}
