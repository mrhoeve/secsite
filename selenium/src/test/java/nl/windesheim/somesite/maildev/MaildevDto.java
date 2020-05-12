package nl.windesheim.somesite.maildev;

import java.util.List;
import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class MaildevDto {
	
	@SerializedName("html")
	@Expose
	private String html;
	@SerializedName("text")
	@Expose
	private String text;
	@SerializedName("headers")
	@Expose
	private Headers headers;
	@SerializedName("subject")
	@Expose
	private String subject;
	@SerializedName("messageId")
	@Expose
	private String messageId;
	@SerializedName("priority")
	@Expose
	private String priority;
	@SerializedName("from")
	@Expose
	private List<From> from = null;
	@SerializedName("to")
	@Expose
	private List<To> to = null;
	@SerializedName("date")
	@Expose
	private String date;
	@SerializedName("id")
	@Expose
	private String id;
	@SerializedName("time")
	@Expose
	private String time;
	@SerializedName("read")
	@Expose
	private Boolean read;
	@SerializedName("envelope")
	@Expose
	private Envelope envelope;
	@SerializedName("source")
	@Expose
	private String source;
	
	public String getHtml() {
		return html;
	}
	
	public void setHtml(String html) {
		this.html = html;
	}
	
	public String getText() {
		return text;
	}
	
	public void setText(String text) {
		this.text = text;
	}
	
	public Headers getHeaders() {
		return headers;
	}
	
	public void setHeaders(Headers headers) {
		this.headers = headers;
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
	
	public String getPriority() {
		return priority;
	}
	
	public void setPriority(String priority) {
		this.priority = priority;
	}
	
	public List<From> getFrom() {
		return from;
	}
	
	public void setFrom(List<From> from) {
		this.from = from;
	}
	
	public List<To> getTo() {
		return to;
	}
	
	public void setTo(List<To> to) {
		this.to = to;
	}
	
	public String getDate() {
		return date;
	}
	
	public void setDate(String date) {
		this.date = date;
	}
	
	public String getId() {
		return id;
	}
	
	public void setId(String id) {
		this.id = id;
	}
	
	public String getTime() {
		return time;
	}
	
	public void setTime(String time) {
		this.time = time;
	}
	
	public Boolean getRead() {
		return read;
	}
	
	public void setRead(Boolean read) {
		this.read = read;
	}
	
	public Envelope getEnvelope() {
		return envelope;
	}
	
	public void setEnvelope(Envelope envelope) {
		this.envelope = envelope;
	}
	
	public String getSource() {
		return source;
	}
	
	public void setSource(String source) {
		this.source = source;
	}
	
}
