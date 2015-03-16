<?php
require_once '';

require_once 'user_class.php';
require_once 'board_class.php';
require_once 'article_class.php';

if(!isset($_GET['no']) || empty($_GET['no']))
	$no = 0;
else
	$no = $_GET['no'];

if(!isset($_GET['pg']) || empty($_GET['pg']))
	$pg = 0;
else
	$pg = $_GET['pg'];



?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>게시판</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="assets/css/common.css">
		<script src="assets/js/common.js"></script>
		<link rel="stylesheet" href="assets/css/index.css">
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<div class='container'>
			<div class='row'>
				
			</div>
		</div>
	</body>
</html>