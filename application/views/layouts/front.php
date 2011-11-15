<!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta name="description" content="">

	<title><?=Title::get()?></title>

	<link href="/favicon.png" rel="shortcut icon" type="image/png">
	
	<?=Asset::styles()?>
	<?=Asset::scripts()?>
	
	
	<!--[if (gte IE 6)&(lte IE 8)]>
	<script src="/javascripts/html5.js"></script>
	<script src="/javascripts/selectivizr.js"></script>
	<noscript><link rel="stylesheet" href="" /></noscript>
	<![endif]-->

	<?=Social_util::init()?>
	
</head>

<body>
	<div id="fb-root"></div>
	<header>
		<figure></figure>
		<h1>miniwini</h1>
		<nav>
			
			<? if (Authly::signed()): ?>
			
			<? else: ?>
			
			<? endif; ?>
			
		</nav>
	</header>

	<div id="content">
			
			




		<!--...................................................
			start of content
		....................................................-->
	
		<?=$content?>

		<!--...................................................
			end of content
		....................................................-->

	</div>


	</div>

	<footer></footer>
	
	
	

</body>
</html>