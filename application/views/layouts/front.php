<!DOCTYPE html>
<!--
            _         _            _         _ 
 _ __ ___  (_) _ __  (_)__      __(_) _ __  (_)
| '_ ` _ \ | || '_ \ | |\ \ /\ / /| || '_ \ | |
| | | | | || || | | || | \ V  V / | || | | || |
|_| |_| |_||_||_| |_||_|  \_/\_/  |_||_| |_||_|
                                    since 2002
-->
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title><?=Title::get()?></title>

	<link href="/favicon.png" rel="shortcut icon" type="image/png">
	
	<?=Asset::styles()?>
	<?=Asset::scripts()?>

</head>

<body>
	
	<header>
		<figure><a href="/"><img src="/img/layout/logo.png" alt="<?=Config::get('miniwini.title')?>"></a></figure>
		
		<nav>
			<ul>
				
				<li><a href="<?=URL::to('board/talk')?>">자유게시판</a></li>
				<li><a href="<?=URL::to('board/share')?>">알짜게시판</a></li>
				<li><a href="<?=URL::to('board/qna')?>">질문&amp;답변</a></li>

				<? if (Authly::signed()): ?>

				<li><a href="<?=URL::to('dashboard')?>"><?=Authly::get_name()?></a></li>
				<li><a href="<?=URL::to('auth/logout')?>">logout</a></li>

				<? else: ?>

				<li><a href="<?=URL::to('auth/login')?>">로그인</a></li>
				<li><a href="<?=URL::to('auth/register')?>">회원 가입</a></li>

				<? endif; ?>

			</ul>

		</nav>
	</header>
	

	
	
	<div id="wrapper">
		
		<div id="pane-left">
			
			<? if (Authly::signed()): ?>
			
			<div id="loginbox">
				<a href="<?=URL::to('dashboard')?>"><figure data-type="avatar-big"><img alt="<?=Authly::get_name()?>" src="<?=Authly::get_avatar_url()?>"></figure>
				<br><?=Authly::get_name()?></a>
			</div>
			
			<? endif; ?>
			
		</div>
		<div id="content">
			
			<!--...................................................
				start of content
			....................................................-->
	
			<?=$content?>

			<!--...................................................
				end of content
			....................................................-->
			
		</div>
		<div id="pane-right">
			
		

			
			
			<div id="visitors">
				
				<ul>
					
					<? foreach (Visitor::all() as $visitor): ?>
				
					<li>
						<?=$visitor->avatar('small')?>
						<strong><?=$visitor->name?></strong>
					</li>
			
					<? endforeach; ?>
					
				</ul>
			</div>
			
		</div>
	</div>
	
	
	
	<footer>
		(c) miniwini / <?=$_SERVER['LARAVEL_ENV']?> / <a href="http://twitter.com/mywizz" data-type="twitter-icon" target="_blank"><span>mywizz on Twitter</span></a> / <a href="http://facebook.com/mywizz" data-type="facebook-icon" target="_blank"><span>mywizz on Facebook</a></span>
	</footer>
	
	<div id="fb-root"></div>
</body>
</html>