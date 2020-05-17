package nl.windesheim.somesite.dto;

public class User {
	private String username;
	private String password;
	private String faSecret;
	
	public User(String username) {
		this.username = username;
		this.password = "";
	}
	
	public User(String username, String password) {
		this.username = username;
		this.password = password;
	}
	
	public String getUsername() {
		return username;
	}
	
	public void setUsername(String username) {
		this.username = username;
	}
	
	public String getPassword() {
		return password;
	}
	
	public void setPassword(String password) {
		this.password = password;
	}
	
	public String getFaSecret() {
		return faSecret;
	}
	
	public void setFaSecret(String faSecret) {
		this.faSecret = faSecret;
	}
}
