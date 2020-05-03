package org.example.untitled.webdriver;

import org.example.untitled.MainPageTest;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.io.File;

public class Webdriver {
	private static Webdriver single_instance = null;
	private final ChromeDriver driver;
	
	private Webdriver() {
		String absolutePath = new File(MainPageTest.class.getClassLoader().getResource("chromedriver.exe").getFile()).getAbsolutePath();
		System.setProperty("webdriver.chrome.driver", absolutePath);
		driver = new ChromeDriver();
	}
	
	public static Webdriver getInstance() {
		if(single_instance == null) {
			single_instance = new Webdriver();
		}
		return single_instance;
	}
	
	public ChromeDriver getDriver() {
		return driver;
	}
	
	public void waitForPageLoad() {
		waitForPageLoad(10);
	}
	
	public void waitForPageLoad(int timeOutInSeconds) {
		ExpectedCondition<Boolean> pageLoadCondition = new
				ExpectedCondition<Boolean>() {
					public Boolean apply(WebDriver driver) {
						return ((JavascriptExecutor)driver).executeScript("return document.readyState").equals("complete");
					}
				};
		WebDriverWait wait = new WebDriverWait(driver, timeOutInSeconds);
		wait.until(pageLoadCondition);
	}
}
