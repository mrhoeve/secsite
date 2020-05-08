package nl.windesheim.somesite.database;

import nl.windesheim.somesite.settings.Settings;
import org.mindrot.jbcrypt.BCrypt;

import java.sql.*;

import static org.assertj.core.api.Assertions.assertThat;
import static org.junit.jupiter.api.Assertions.fail;

public class Database {
	private static Database single_instance = null;
	private static String DbUrl;
	
	private Database() {
		DbUrl = "jdbc:mysql://" + Settings.MYSQL_HOST + ":" + Settings.getInstance().getMysqlPort() + "/" + Settings.MYSQL_DB ;
				//"?serverTimezone=Europe/Amsterdam";
	}
	
	public static Database getInstance() {
		if (single_instance == null) {
			single_instance = new Database();
		}
		return single_instance;
	}
	
	public void checkIfPasswordsAreHashed() {
		String username;
		String password;
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     Statement statement = connection.createStatement()) {
			String query = "SELECT username, password FROM User";
			ResultSet rs = statement.executeQuery(query);
			while(rs.next()) {
				username = rs.getString("username");
				password = rs.getString("password");
				if(!password.startsWith("$2y$")) {
					setPasswordForUser(username, password);
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
	}
	
	public Boolean setPasswordForUser(String username, String password) {
		try (Connection connection = DriverManager.getConnection(DbUrl, Settings.MYSQL_USER, Settings.MYSQL_PASS);
		     PreparedStatement statement = connection.prepareStatement("UPDATE User SET password = ? WHERE username = ?")) {
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
		     PreparedStatement statement = connection.prepareStatement("UPDATE User SET disabled = ? WHERE username = ?")) {
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
		     PreparedStatement statement = connection.prepareStatement("UPDATE User SET changepwonl = ? WHERE username = ?")) {
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
		     PreparedStatement statement = connection.prepareStatement("UPDATE User SET fasecret = null WHERE username = ?")) {
			statement.setString(1, username);
			statement.execute();
			return statement.getUpdateCount() == 1;
		} catch (SQLException e) {
			e.printStackTrace();
			fail();
		}
		return false;
	}
	
	public Boolean resetUserForLogin(String username, String password) {
		assertThat(setPasswordForUser(username, password)).isTrue();
		assertThat(setDisabledForUser(username, false)).isTrue();
		assertThat(setChangePwONLForUser(username, false)).isTrue();
		assertThat(remove2FASecret(username)).isTrue();
		return true;
	}
	
}
