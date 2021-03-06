package nl.windesheim.somesite.webdriver;

import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.PageLoadStrategy;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.io.File;

public class Webdriver {
	private static Webdriver single_instance = null;
	private final ChromeDriver driver;
	
	private Webdriver() {
		String absolutePath = new File(Webdriver.class.getClassLoader().getResource("chromedriver.exe").getFile()).getAbsolutePath();
		System.setProperty("webdriver.chrome.driver", absolutePath);
		driver = new ChromeDriver(options());
	}
	
	public static Webdriver getInstance() {
		if(single_instance == null) {
			single_instance = new Webdriver();
			
			// Voeg een hook toe zodat we de driver kunnen sluiten bij het exiten
			Runtime.getRuntime().addShutdownHook(new Thread()
			{
				public void run()
				{
					Webdriver.getInstance().getDriver().quit();
				}
			});
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
	
	private ChromeOptions options() {
		// Inspiratie voor deze opties: https://stackoverflow.com/questions/48450594/selenium-timed-out-receiving-message-from-renderer/52340526
		ChromeOptions options = new ChromeOptions();
		// ChromeDriver is just AWFUL because every version or two it breaks unless you pass cryptic arguments
		//AGRESSIVE: options.setPageLoadStrategy(PageLoadStrategy.NONE); // https://www.skptricks.com/2018/08/timed-out-receiving-message-from-renderer-selenium.html
		options.setPageLoadStrategy(PageLoadStrategy.NONE);
//		options.addArguments("start-maximized"); // https://stackoverflow.com/a/26283818/1689770
		options.addArguments("enable-automation"); // https://stackoverflow.com/a/43840128/1689770
//		options.addArguments("--headless"); // only if you are ACTUALLY running headless
		options.addArguments("--no-sandbox"); //https://stackoverflow.com/a/50725918/1689770
		options.addArguments("--disable-infobars"); //https://stackoverflow.com/a/43840128/1689770
		options.addArguments("--disable-dev-shm-usage"); //https://stackoverflow.com/a/50725918/1689770
		options.addArguments("--disable-browser-side-navigation"); //https://stackoverflow.com/a/49123152/1689770
		options.addArguments("--disable-gpu"); //https://stackoverflow.com/questions/51959986/how-to-solve-selenium-chromedriver-timed-out-receiving-message-from-renderer-exc
		options.addArguments("--whitelisted-ips=''"); //https://stackoverflow.com/questions/40305669/selenium-webdriver-3-0-1-chromedriver-exe-2-25-whitelisted-ips/44629135
		return options;
	}
	
	
}
