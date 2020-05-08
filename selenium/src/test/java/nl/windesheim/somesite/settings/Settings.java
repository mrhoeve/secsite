package nl.windesheim.somesite.settings;

public class Settings {
	// DATABASE CONNECTIONS
	public static final String MYSQL_HOST = "localhost";
	public static final String MYSQL_DB = "security";
	public static final String MYSQL_USER = "secsave";
	public static final String MYSQL_PASS = "4aCZNGdqNqMA4Pee";
	private static Settings single_instance = null;
	private String baseUrl;
	private String sqlPort;
	
	private Settings() {
	
	}
	
	public void setSettings(String wwwPort, String sqlPort) {
		if(baseUrl != null && this.sqlPort != null) {
			throw new RuntimeException("Mag slechts eenmaal gezet worden");
		}
		baseUrl = "http://localhost:" + wwwPort + "/";
		this.sqlPort = sqlPort;
	}

	public static Settings getInstance() {
		if (single_instance == null) {
			single_instance = new Settings();
		}
		return single_instance;
	}

	public String getBaseUrl() {
		return baseUrl;
	}

	public String getMysqlPort() {
		return sqlPort;
	}
}
