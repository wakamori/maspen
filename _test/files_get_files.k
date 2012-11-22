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
		String url = "http://localhost/moodle/login/token.php";
		url = url + "?username=" + username;
		url = url + "&password=" + password;
		url = url + "&service="  + service;
	
		String str = "";
		c.setOpt(CURLOPT_URL, url);
		c.setOpt(CURLOPT_WRITEDATA, str);
		c.perform();
		token = Json.parse(str).getString("token");
	}

	void run(String function, String params) {
		String url = "http://localhost/moodle/webservice/rest/server.php";
		url = url + "?wstoken=" + token;
		url = url + "&wsfunction=" + function;
		url = url + params;
	//	url = url + "&moodlewsrestformat=json";
		System.p(url);
		String str = "";
		c.setOpt(CURLOPT_URL, url);
		c.setOpt(CURLOPT_WRITEDATA, str);
		c.perform();
		System.p(str);
	}
}

void main() {
	String username = "admin";
	String password = "@Index1206";
	String service  = "test";
	
	String token    = "";
	
	WebServiceClient client;
	if (token == "") {
		client = new WebServiceClient(username, password, service);
	}
	else {
		client = new WebServiceClient(token);
	}
	
	String function = "core_files_get_files";
	String params  = "&contextid=5&component=user&filearea=private&itemid=0&filepath=/&filename=test.txt";
//[{"component":"user","contextid":"5","userid":"2","filearea":"private","filename":"test.txt","filepath":"\/","itemid":0,"license":"allrightsreserved","author":"Kanemura Yoshiaki","source":""}]
	client.run(function, params);
}

main();