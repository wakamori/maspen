import("apache");
import("cstyle");
import("js4.string");
import("konoha.array");
import("konoha.class");

load("./decodeURI.k");

class CGI {
	String[] querylist;

	CGI(Request req) {
		this.querylist = req.getArgs().split(/&/);
	}

	String getParam(String key) {
		int i = 0;
		while(i < querylist.getSize()) {
			String[] k_v = querylist[i].split(/=/);
			if(k_v[0].decodeURI() == key) {
				return k_v[1].decodeURI();
			}
			i = i + 1;
		}
		return null;
	}
}
