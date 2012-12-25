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
				url: PATH + "k/k2js.k",
				dataType: "text",
				data: myCodeMirror.getValue(),
				success: function(res) {
					$("#result").text(res);
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
				$("#result").text(res);
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
				$("#result").text(res);
				prettyPrint();
			}
		});
	});
});
