import("konoha.string");
import("konoha.class");
import("konoha.new");
import("jansson");
import("curl");
import("konoha.bytes");
import("posix.file");

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

	void run(String relativepath, String path) {
		String url = "http://localhost/moodle/webservice/pluginfile.php";
		url = url + relativepath;
		url = url + "?token=" + token;
		Bytes buf = new Bytes(0);
		c.setOpt(CURLOPT_URL, url);
		c.setOpt(CURLOPT_WRITEDATA, buf);
		c.perform();
		FILE fp = System.fopen(path, "w");
		fp.write(buf, 0, buf.getSize()-1);
		fp.close();
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
	
	String relativepath = "/5/user/private/folder1/p146-roessling.pdf";
	String path  = "/home/yoshiaki/p146-roessling.pdf";
	
	client.run(relativepath, path);
}

main();

//CHECK! moodle.lib=>get_directory_list