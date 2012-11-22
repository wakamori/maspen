import("konoha.bytes");
import("konoha.string");
import("konoha.class");
import("konoha.new");
import("jansson");
import("curl");

class WebServiceClient {
	String username;
	String password;
	String service;
	String token;
	Curl c = new Curl();
	
	WebServiceClient(String username, String password, String service) {
		this.username = username;
		this.password = password;
		this.service  = service;
	    get_token();
	}
	
	WebServiceClient(String token) {
		this.token = token;
	}
  
	void get_token(){
		String url = "http://localhost/moodle/login/get_token.php";
		url = url + "?username=" + username;
		url = url + "&password=" + password;
		url = url + "&service="  + service;
	
		Bytes buf = new Bytes(0);
		c.setOpt(CURLOPT_URL, url);
		c.setOpt(CURLOPT_WRITEDATA, buf);
		c.perform();
		token = buf.asString();
	}

	void run(String function, String params) {
		String url = "http://localhost/moodle/webservice/rest/server.php";
		url = url + "?wstoken=" + token;
		url = url + "&wsfunction=" + function;
		url = url + params;
		url = url + "&moodlewsrestformat=json";
	
		Bytes buf = new Bytes(0);
		c.setOpt(CURLOPT_URL, url);
		c.setOpt(CURLOPT_WRITEDATA, buf);
		c.perform();
		System.p(buf.asString());
	}
}

void main() {
	String username = "";
	String password = "";
	String service  = "";
	
	String token    = "";
	
	WebServiceClient client;
	if (token == "") {
		client = new WebServiceClient(username, password, service);
	}
	else {
		client = new WebServiceClient(token);
	}
	
	String function = "";
	String params  = "&";
	
	client.run(function, params);
}

main();