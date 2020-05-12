package nl.windesheim.somesite.maildev;

import java.util.List;
import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Envelope {
	
	@SerializedName("from")
	@Expose
	private From_ from;
	@SerializedName("to")
	@Expose
	private List<To_> to = null;
	@SerializedName("host")
	@Expose
	private String host;
	@SerializedName("remoteAddress")
	@Expose
	private String remoteAddress;
	
	public From_ getFrom() {
		return from;
	}
	
	public void setFrom(From_ from) {
		this.from = from;
	}
	
	public List<To_> getTo() {
		return to;
	}
	
	public void setTo(List<To_> to) {
		this.to = to;
	}
	
	public String getHost() {
		return host;
	}
	
	public void setHost(String host) {
		this.host = host;
	}
	
	public String getRemoteAddress() {
		return remoteAddress;
	}
	
	public void setRemoteAddress(String remoteAddress) {
		this.remoteAddress = remoteAddress;
	}
	
}

