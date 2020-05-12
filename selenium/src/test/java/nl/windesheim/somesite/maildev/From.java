package nl.windesheim.somesite.maildev;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class From {
	
	@SerializedName("address")
	@Expose
	private String address;
	@SerializedName("name")
	@Expose
	private String name;
	
	public String getAddress() {
		return address;
	}
	
	public void setAddress(String address) {
		this.address = address;
	}
	
	public String getName() {
		return name;
	}
	
	public void setName(String name) {
		this.name = name;
	}
	
}

