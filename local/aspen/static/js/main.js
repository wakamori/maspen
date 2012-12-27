$(function() {
	/* editor */
	var myCodeMirror = CodeMirror(function(elt) {
		$("#editor").replaceWith(elt);
	}, {
		value: $("#editor").val(),
		mode: "text/x-konoha",
		lineNumbers: true
	});

	/* button actions */
	$("#button-run").click(function() {
		if(myCodeMirror.getValue() != sessionStorage.getItem("previousValue") ||
			$("#result").text() == String.fromCharCode(160)) {
			$.ajax({
				type: "GET",
				url: PATH + "k/k2js.cgi",
				dataType: "text",
				data: myCodeMirror.getValue(),
				success: function(res) {
					$("#console").text(res);
					prettyPrint();
				}
			});
			sessionStorage.setItem("previousValue", myCodeMirror.getValue());
		}
	});

	$("#button-submit").click(function() {
		$.ajax({
			type: "GET",
			url: ROOTURL + "webservice/rest/server.php",
			dataType: "text",
			data: {
				wstoken: "1d6440b99800118436b01942f0e3d76e",
				wsfunction: "local_exfunctions_submit_assignment",
				moodlewsrestformat: "json",
				id: ID,
				userid: USERID,
				text: myCodeMirror.getValue()
			},
			success: function(res) {
				var obj = JSON.parse(res);
				$("#status").text(obj.status);
				$("#duedate").text(parse_time(obj.duedate));
				$("#timemodified").text(parse_time(obj.timemodified));
				$("#text").text(obj.text);
				$("#modal-status").modal("show");
				prettyPrint();
			}
		});
	});

	$("#button-status").click(function() {
		$.ajax({
			type: "GET",
			url: ROOTURL + "webservice/rest/server.php",
			dataType: "text",
			data: {
				wstoken: "1d6440b99800118436b01942f0e3d76e",
				wsfunction: "local_exfunctions_view_assignment",
				moodlewsrestformat: "json",
				id: ID,
				userid: USERID
			},
			success: function(res) {
				var obj = JSON.parse(res);
				$("#status").text(obj.status);
				$("#duedate").text(parse_time(obj.duedate));
				$("#timemodified").text(parse_time(obj.timemodified));
				$("#text").text(obj.text);
				$("#modal-status").modal("show");
				prettyPrint();
			}
		});
	});

	$("#button-ranking").click(function() {
/*		$.ajax({
			type: "GET",
			url: "get_data.php",
			dataType: "text",
			data: {
				wstoken: "1d6440b99800118436b01942f0e3d76e",
				wsfunction: "local_exfunctions_view_assignment",
				moodlewsrestformat: "json",
				id: ID,
				userid: USERID
			},
			success: function(res) {
				var obj = JSON.parse(res);
				$("#status").text(obj.status);
				$("#duedate").text(parse_time(obj.duedate));
				$("#timemodified").text(parse_time(obj.timemodified));
				$("#text").text(obj.text);
				$("#modal-status").modal("show");
				prettyPrint();
			}
		});*/
		$("#modal-ranking").modal("show");
	});

	function parse_time(ary) {
		var time = "";
		time += ary.year  + "年";
		time += ary.month + "月";
		time += ary.day   + "日";
		time += ary.hours + "時";
		time += ary.minutes + "分";
		time += ary.seconds + "秒";

		return time;
	}
});
