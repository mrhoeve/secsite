package nl.windesheim.somesite.database;

import nl.windesheim.somesite.settings.Settings;
import org.mindrot.jbcrypt.BCrypt;

import java.sql.*;

import static org.assertj.core.api.Assertions.assertThat;
import static org.junit.jupiter.api.Assertions.fail;

public class Database {
	private static Database single_instance = null;
	private static String DbUrl;
	private static String smtpPort = null;
	
	private Database() {
		DbUrl = "jdbc:mysql://" + Settings.MYSQL_HOST + ":" + Settings.MYSQL_PORT + "/" + Settings.MYSQL_DB +
				"?serverTimezone=Europe/Amsterdam";
	}
	
	public static Database getInstance() {
		if (single_instance == null) {
			single_instance = new Database();
		}
		return single_instance;
	}
	
	public void deleteUserIfExists(String username) {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("DELETE FROM user WHERE username = ?")) {
			statement.setString(1, username);
			statement.execute();
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
	}
	
	public Boolean setPasswordForUser(String username, String password) {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("UPDATE user SET password = ? WHERE username = ?")) {
			statement.setString(1, BCrypt.hashpw(password, BCrypt.gensalt()));
			statement.setString(2, username);
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	public Boolean setDisabledForUser(String username, Boolean disabled) {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("UPDATE user SET disabled = ? WHERE username = ?")) {
			statement.setInt(1, disabled ? 1 : 0);
			statement.setString(2, username);
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	public Boolean setChangePwONLForUser(String username, Boolean changepwonl) {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("UPDATE user SET changepwonl = ? WHERE username = ?")) {
			statement.setInt(1, changepwonl ? 1 : 0);
			statement.setString(2, username);
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	public Boolean remove2FASecret(String username) {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("UPDATE user SET fasecret = null WHERE username = ?")) {
			statement.setString(1, username);
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	public Boolean setSmtpPort(String port) {
		getCurrentSmtpPort();
		if(smtpPort == null) {
			try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
			     PreparedStatement statement = connection.prepareStatement("INSERT INTO setting (`key`, value) VALUES ('smtp_port', ?)")) {
				statement.setString(1, port);
				statement.execute();
				return statement.getUpdateCount() == 1;
			} catch (SQLException e) {
				e.printStackTrace();
				fail();
			}
			return false;
		}
		
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("UPDATE setting SET value = ? WHERE `key` = 'smtp_port'")) {
			statement.setString(1, port);
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	public Boolean resetSmtpPort() {
		if(smtpPort != null) {
			return setSmtpPort(smtpPort);
		}
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("DELETE FROM setting WHERE `key` = 'smtp_port'")) {
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	private void getCurrentSmtpPort() {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("SELECT value FROM setting WHERE `key`='smtp_port'");
		     ResultSet rs = statement.executeQuery()) {
			while(rs.next()) {
				smtpPort = rs.getString("value");
			}
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
	}
	
	public Boolean resetUserForLogin(String username, String password) {
		return resetUserForLogin(username, password, false);
	}
	
	public Boolean resetUserForLogin(String username, String password, Boolean setChangePwONL) {
		assertThat(setPasswordForUser(username, password)).isTrue();
		assertThat(setDisabledForUser(username, false)).isTrue();
		assertThat(setChangePwONLForUser(username, setChangePwONL)).isTrue();
		assertThat(remove2FASecret(username)).isTrue();
		return true;
	}
	
}
