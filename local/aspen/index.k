load("./k/template.k");
load("./k/CGI.k");

@Public int System.handler(Request req) {
	req.setContentType("text/html");
	req.setContentEncoding("utf-8");
	Template t = Template.getTemplate("index.html");
	CGI cgi = new CGI(req);
	t.set("ID", cgi.getParam("id"));
	t.set("USERID", cgi.getParam("userid"));
	t.set("PATH", "http://konoha.ubicg.ynu.ac.jp/maspen/local/aspen/");
	t.set("ROOTURL", "http://konoha.ubicg.ynu.ac.jp/maspen/");
	t.set("TITLE", "Konoha");
	t.set("COPY", "Konoha Project");
	t.set("URL", req.getArgs());
	req.puts(t.render());
	return OK;
}
