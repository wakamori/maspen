import("konoha");
import("konoha.bytes");
import("konoha.new");
import("jansson");
import("curl");

String Bytes.asString() {
	String ret = "";
	int i = 0;
	while(i < this.getSize()) {
		ret = ret + String.fromCharCode(this.get(i));
		i = i + 1;
	}
	return ret;
}

class WebServiceClient {
	String username;
	String password;
	String service;
	String token;
	Curl c;

	WebServiceClient(String username, String password, String service) {
		this.username = username;
		this.password = password;
		this.service  = service;
		this.c = new Curl();System.p("hello");
		get_token();
	}

	WebServiceClient(String token) {
		this.token = token;
		this.c = new Curl();
	}
  
	void get_token(){
		System.p("konoha");
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
	String username = "admin";
	String password = "@Index1206";
	String service  = "test";

	String token    = "";

	WebServiceClient client = null;
	if (token == "") {
		client = new WebServiceClient(username, password, service);
	}
	else {
		client = new WebServiceClient(token);
	}
	
	String function = "core_user_get_users_by_id";
	String params  = "&userids[0]=2";

	client.run(function, params);
}

main();
