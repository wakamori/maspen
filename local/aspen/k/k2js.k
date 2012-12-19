import("apache");
import("cstyle", "null");
import("cstyle", "while");
import("dscript.subproc");
import("konoha.array");
import("konoha.file");
import("konoha.string");
import("posix.path");
import("posix.process");

load("./decodeURI.k");

@Public int System.handler(Request req) {
	req.setContentType("text/plain");
	req.setContentEncoding("utf-8");
	SubProc sp = new SubProc("mktemp");
	sp.setArgumentList(["-q", "/tmp/js.XXXXXX"]);
	sp.bg();
	String filename = sp.communicate("")[0].trim();
	FILE tmp = new FILE(filename, "w");
	String input = req.getArgs().trim().decodeURI();
	tmp.print(input);
	tmp.close();
	sp = new SubProc("/usr/local/bin/minikonoha");
	sp.setArgumentList(["-MJavaScript", "-c", filename]);
	sp.bg();
	req.puts(sp.communicate("")[0].trim());
	System.unlink(filename);
	return OK;
}
