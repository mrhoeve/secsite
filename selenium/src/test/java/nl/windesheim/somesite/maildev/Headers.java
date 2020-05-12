package nl.windesheim.somesite.maildev;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Headers {
	
	@SerializedName("date")
	@Expose
	private String date;
	@SerializedName("to")
	@Expose
	private String to;
	@SerializedName("from")
	@Expose
	private String from;
	@SerializedName("subject")
	@Expose
	private String subject;
	@SerializedName("message-id")
	@Expose
	private String messageId;
	@SerializedName("x-mailer")
	@Expose
	private String xMailer;
	@SerializedName("mime-version")
	@Expose
	private String mimeVersion;
	@SerializedName("content-type")
	@Expose
	private String contentType;
	@SerializedName("content-transfer-encoding")
	@Expose
	private String contentTransferEncoding;
	
	public String getDate() {
		return date;
	}
	
	public void setDate(String date) {
		this.date = date;
	}
	
	public String getTo() {
		return to;
	}
	
	public void setTo(String to) {
		this.to = to;
	}
	
	public String getFrom() {
		return from;
	}
	
	public void setFrom(String from) {
		this.from = from;
	}
	
	public String getSubject() {
		return subject;
	}
	
	public void setSubject(String subject) {
		this.subject = subject;
	}
	
	public String getMessageId() {
		return messageId;
	}
	
	public void setMessageId(String messageId) {
		this.messageId = messageId;
	}
	
	public String getXMailer() {
		return xMailer;
	}
	
	public void setXMailer(String xMailer) {
		this.xMailer = xMailer;
	}
	
	public String getMimeVersion() {
		return mimeVersion;
	}
	
	public void setMimeVersion(String mimeVersion) {
		this.mimeVersion = mimeVersion;
	}
	
	public String getContentType() {
		return contentType;
	}
	
	public void setContentType(String contentType) {
		this.contentType = contentType;
	}
	
	public String getContentTransferEncoding() {
		return contentTransferEncoding;
	}
	
	public void setContentTransferEncoding(String contentTransferEncoding) {
		this.contentTransferEncoding = contentTransferEncoding;
	}
	
}

