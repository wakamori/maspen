/*
 * template.k : template script for output initializr javascript
 */

import("apache");
import("cstyle", "null");
import("cstyle", "while");
import("js4.array");
import("java2.class");
import("konoha.const");
import("konoha.file");
import("konoha.foreach");
import("js4.string");

const BASE_PATH="/var/www/html/maspen/local/aspen/static";

int Array_indexOf(Array[String] sa, String val) {
	int ret = -1;
	int i = 0;
	while(i < sa.getlength()) {
		if(sa[i] == val) {
			return i;
		}
		i = i + 1;
	}
	return ret;
}

class Template {
	File file;
	String[] params;
	String[] paramkeys;

	Template(File f) {
		file = f;
		params = [];
		paramkeys = [];
	}

	@Public @Static Template getTemplate(String templateName) {
		return new Template(new File(BASE_PATH + "/" + templateName, "r"));
	}

	@Public void set(String key, String val) {
		if(Array_indexOf(paramkeys, key) == -1) {
			paramkeys.add(key);
			params.add(val);
		}
		else {
			params[Array_indexOf(paramkeys, key)] = val;
		}
	}

	String applyParams(String str) {
		String ret = str;
		int i = 0;
		while(i < paramkeys.getlength()) {
			ret = ret.replace("%" + paramkeys[i] + "%", params[i]);
			i = i + 1;
		}
		return ret;
	}

	@Public String render() {
		String ret = "";
		String buf = file.readLine();
		while(buf.isNotNull()) {
			ret = ret + buf + "\n";
			buf = file.readLine();
		}
		return applyParams(ret);
	}
}
