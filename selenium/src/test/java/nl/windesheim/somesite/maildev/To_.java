package nl.windesheim.somesite.maildev;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class To_ {
	
	@SerializedName("address")
	@Expose
	private String address;
	@SerializedName("args")
	@Expose
	private Boolean args;
	
	public String getAddress() {
		return address;
	}
	
	public void setAddress(String address) {
		this.address = address;
	}
	
	public Boolean getArgs() {
		return args;
	}
	
	public void setArgs(Boolean args) {
		this.args = args;
	}
	
}