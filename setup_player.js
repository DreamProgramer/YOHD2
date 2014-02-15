$(function () {
  $('#setup').click(function () {
		// プレイヤー登録
		$.ajax({
			type: "POST",
			scriptCharset: 'utf-8',
			url: '../proxy.php',
			data: { "camera": 0 },
			success: function (res) {
				var json = $.parseJSON(res);
				if (json.status == "ERROR") {
						alert(json.result);
						return;
				}
				console.log("player1", json);
				
				// player 2
				$.ajax({
					type: "POST",
					scriptCharset: 'utf-8',
					url: '../proxy.php',
					data: { "camera": 2 },
					success: function (res) {
							var json = $.parseJSON(res);
							if (json.status == "ERROR") {
									alert(json.result);
									return;
							}
							console.log("player2", json);
							
					},
					error: function () {
							$(".wait_span").css("display", "none");
							alert("サーバエラー");
					}
				});
			},
			error: function () {
					$(".wait_span").css("display", "none");
					alert("サーバエラー");
			}
		});
	});
});
