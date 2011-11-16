<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title><?=Title::get()?></title>

	<link href="/favicon.png" rel="shortcut icon" type="image/png">
	
	<?=Asset::styles()?>
	<?=Asset::scripts()?>

</head>

<body>
	<div id="fb-root"></div>
	<header>
		<figure></figure>
		<nav>
			<ul>
				
				<? if (Authly::signed()): ?>
			
				<li>welcome, <a href="<?=URL::to('dashboard')?>"><?=Authly::get_name()?></a></li>
				<ii><a href="<?=URL::to('auth/logout')?>">logout</a></li>
				
				<? else: ?>
				
				<li><a href="<?=URL::to('auth/login')?>">로그인</a></li>
				<li><a href="<?=URL::to('auth/register')?>">회원 가입</a></li>
			
				<? endif; ?>
			
			</ul>
			
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


	<footer></footer>
</body>
</html>