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
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="mywizz">
	<meta name="description" content="">
	
	<link href="/favicon.png" rel="shortcut icon" type="image/png">
	
	<? if (Session::get('mobile')): ?>
	
	<link href="/css/mobile.css" media="screen" rel="stylesheet">
	
	<? else: ?>
	
	<link href="/css/miniwini.css" media="screen" rel="stylesheet">
	
	<? endif; ?>

	
	<script src="/javascripts/jquery.js"></script>
	
	<? if (Authly::signed()): ?>
	
	<script src="/javascripts/miniwini.js"></script>
	<script src="/javascripts/commently.js"></script>
	
	<? endif; ?>
	
	
	<!--[if (gte IE 6)&(lte IE 8)]>
	<script src="/javascripts/html5shiv.js"></script>
	<script src="/javascripts/selectivizr.js"></script>
	<![endif]-->
	
	
	
</head>

<body>
	
	<header>
		<figure>
			<a href="/"><img src="/img/layout/miniwini_logo.png" alt="<?=Config::get('miniwini.title')?>"></a>
			<figcaption><?=Config::get('miniwini.description')?></figcaption>
		</figure>
		
		<nav>
			<ul>
				
				<li><a href="<?=URL::to('board/talk')?>">자유게시판</a></li>
				<li><a href="<?=URL::to('board/share')?>">알짜게시판</a></li>
				<li><a href="<?=URL::to('board/qna')?>">질문&amp;답변</a></li>

				<? if (Authly::signed()): ?>
				
				<li><span id="notifications-count" onclick="miniwini.notifications()"></span><div id="notifications"></div></li>
				<li><a href="<?=URL::to('dashboard')?>">Dashboard</a></li>
				<li data-align="right"><a href="<?=URL::to('auth/logout')?>">logout</a></li>

				<? else: ?>

				<li data-align="right"><a href="<?=URL::to('auth/login')?>">로그인</a></li>
				<li data-align="right"><a href="<?=URL::to('auth/register')?>">회원 가입</a></li>

				<? endif; ?>

			</ul>

		</nav>
	</header>
	

	
	
	<div id="wrapper">
		
		<aside id="pane-left">
			
			<? if (Authly::signed()): ?>
			
			<div id="loginbox">
				<figure data-type="avatar-medium"><a href="<?=URL::to('dashboard')?>"><img alt="<?=Authly::get_name()?>" src="<?=Authly::get_avatar_url()?>"></a></figure>
	
			</div>
			
			<? endif; ?>
			
		</aside>
		<div id="content">
			
			<? if (Session::has('errors')): ?>

			<div data-group="error">
				
				<?=Session::get('errors')?>
				
			</div>

			<? endif; ?>
			
			<? if (Session::has('notification')): ?>

			<div data-group="notification">
				
				<?=Session::get('notification')?>
				
			</div>

			<? endif; ?>
			
			<!--== Content ==-->
	
			<?=$content?>

			<!--== Content ==-->
			
		</div>
		<aside id="pane-right">
			
			<? $visitors = Visitor::all(); ?>
			
			<? if ( ! empty($visitors)): ?>
			
			<div id="visitors">
				
				<? if ( ! empty($visitors['users'])): ?>
				
				<ul>
					
					<? foreach ($visitors['users'] as $visitor): ?>

					<li>
						<?=$visitor->avatar('small')?>
						<strong><?=$visitor->name?></strong>
					</li>
					
					<? endforeach; ?>
					
				</ul>
				
				<? endif; ?>
				
				<? if ($visitors['guest_count'] > 0): ?>

				<span id="guest-count">+<?=$visitors['guest_count']?></span>

				<? endif; ?>
				
			</div>
			

			
			
			<? endif; ?>
			
		</aside>
	</div>
	
	
	
	<footer>
		(c) miniwini / <?=$_SERVER['LARAVEL_ENV']?> / 
		<a href="http://twitter.com/mywizz" data-type="twitter-icon" target="_blank"><span>mywizz on Twitter</span></a> / 
		<a href="http://facebook.com/mywizz" data-type="facebook-icon" target="_blank"><span>mywizz on Facebook</span></a>

		
	</footer>
	
	<div id="fb-root"></div>
	
	
	<script type="text/javascript">
	window._gaq = [['_setAccount','UA-297919-5'],['_trackPageview'],['_trackPageLoadTime']];
	$(function(){
		var ga = document.createElement('script'); ga.type = 'text/javascript'; 
		ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; 
		s.parentNode.insertBefore(ga, s);
	})();
	</script>
</body>
</html>