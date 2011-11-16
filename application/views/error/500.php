<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title>500 Error</title>
</head>

<body>
	<h1>Error</h1>
	
	<? if (isset($message)): ?>
	
	<?=$message?>
	
	<? endif; ?>
	
</body>
</html>