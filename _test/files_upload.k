import("konoha.bytes");
import("konoha");
import("konoha.string");
import("konoha.array");
import("konoha.class");
import("konoha.map");
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

	void run(String imagepath, String filepath) {
		String url = "http://localhost/moodle/webservice/upload.php";
		url = url + "?token=" + token;
		url = url + "&filepath=" + filepath;
		Bytes buf = new Bytes(0);
		c.setOpt(CURLOPT_HEADER, 0);
		c.setOpt(CURLOPT_VERBOSE, true);
		c.setOpt(CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		c.setOpt(CURLOPT_URL, url);
		c.setOpt(CURLOPT_POST, true);
		c.setOpt(CURLOPT_POSTFIELDS, "{\"file_box\": " + imagepath + "}");
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
	
	WebServiceClient client;
	if (token == "") {
		client = new WebServiceClient(username, password, service);
	}
	else {
		client = new WebServiceClient(token);
	}
	
	String imagepath = "@/home/yoshiaki/uptest2.txt";
	String filepath  = "/";
	
	client.run(imagepath, filepath);
}

main();