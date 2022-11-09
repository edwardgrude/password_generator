<?php
	// php called by ajax
	if (!empty($_POST)) {
		$length = isset($_POST['length_param']) ? (int)$_POST['length_param'] : 15;

		$characters_a = 'abcdefghjkmnpqrstuvwxyz';
		$characters_b = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		$characters_c = '346789';
		$characters_d = '&#!*.;:@';

		$randomString = '';

		// Lower case characters
		$charactersLength = strlen($characters_a);
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters_a[rand(0, $charactersLength - 1)];
		}

		$already_replaced = array();

		// 1/3 upper case characters
		$length_third = (int)($length / 3);
		while (count($already_replaced) < $length_third) {
			$pos = rand(0, $length - 1);
			if (!in_array($pos, $already_replaced)) {
				$already_replaced[] = $pos;
				$randomString[$pos] = $characters_b[rand(0, strlen($characters_b) - 1)];
			}
		}

		// 1/3 numbers
		while (count($already_replaced) < ($length_third * 2)) {
			$pos = rand(0, $length - 1);
			if (!in_array($pos, $already_replaced)) {
				$already_replaced[] = $pos;
				$randomString[$pos] = $characters_c[rand(0, strlen($characters_c) - 1)];
			}
		}

		// 1/6 special characters
		while (count($already_replaced) < (($length_third * 2) + ($length_third / 2))) {
			$pos = rand(0, $length - 1);
			if (!in_array($pos, $already_replaced)) {
				$already_replaced[] = $pos;
				$randomString[$pos] = $characters_d[rand(0, strlen($characters_d) - 1)];
			}
		}

		exit($randomString);
	}
?>
<?php $length = isset($_GET['length']) && $_GET['length'] > 0 && $_GET['length'] <= 40 ? $_GET['length'] : 15; ?>
<!doctype html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="styles.css" />
		<title>Password-Generator</title>
	</head>
	<body>
		<p id='random_string' onclick='setClipboardText();'>...</p>
		<div class="slidecontainer">
			<input type="range" min="1" max="40" value="<?php echo $length ?>" class="slider"><p><?php echo $length ?></p>
			<div style='clear:both;'></div>
		</div>
		<p id='description'>Click password to copy to clipboard<br/>and to generate the next password.</p>
		<script type='text/javascript'>
			function setClipboardText() {
				text = document.getElementById('random_string').innerHTML;
				var id = "mycustom-clipboard-textarea-hidden-id";
				var existsTextarea = document.getElementById(id);

				if (!existsTextarea) {
					console.log("Creating textarea");
					var textarea = document.createElement("textarea");
					textarea.id = id;
					// Place in top-left corner of screen regardless of scroll position.
					textarea.style.position = 'fixed';
					textarea.style.top = 0;
					textarea.style.left = 0;

					// Ensure it has a small width and height. Setting to 1px / 1em
					// doesn't work as this gives a negative w/h on some browsers.
					textarea.style.width = '1px';
					textarea.style.height = '1px';

					// We don't need padding, reducing the size if it does flash render.
					textarea.style.padding = 0;

					// Clean up any borders.
					textarea.style.border = 'none';
					textarea.style.outline = 'none';
					textarea.style.boxShadow = 'none';

					// Avoid flash of white box if rendered for any reason.
					textarea.style.background = 'transparent';
					document.querySelector("body").appendChild(textarea);
					console.log("The textarea now exists :)");
					existsTextarea = document.getElementById(id);
				} else {
					console.log("The textarea already exists :3")
				}

				existsTextarea.value = text;
				existsTextarea.select();

				try {
					var status = document.execCommand('copy');
					if (!status){
						console.error("Cannot copy text");
					} else {
						console.log("The text is now on the clipboard");
					}
				} catch (err) {
					console.log('Unable to copy.');
				}

				const length = $('.slider').val();
				$.ajax({
					type:'post',
					url:'password.php',
					data:'length_param=' + length,
					success:function(data) {
						$('#random_string').html(data);
					}
				});
			}

			$(document).ready(function() {
				$.ajax({
					type:'post',
					url:'password.php',
					data:'length_param=' + $('.slider').val(),
					success:function(data) {
						$('#random_string').html(data);
					}
				});

				$('.slider').on('input', function(e) {
					const length = $(this).val();
					$('.slidecontainer p').html(length);

					if (length < 12) $(this).addClass('warning');
					else $(this).removeClass('warning');

					$.ajax({
						type:'post',
						url:'password.php',
						data:'length_param=' + length,
						success:function(data) {
							$('#random_string').html(data);
						}
					});
				});
			});
		</script>
	</body>
</html>
