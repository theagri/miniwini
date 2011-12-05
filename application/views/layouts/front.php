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
	<link rel="stylesheet" href="/css/miniwini.css" type="text/css">
	
	
	
	<style type="text/css">
	
	<? if ( ! empty($_COOKIE['x'])): ?>
	
	#wrapper
	{
		left:<?=$_COOKIE['x']?>px;
	}
	
	<? endif; ?>
	
	<? if (Authly::signed() and Authly::pref('font')):?>
	
	body
	{
		font-family:<?=Authly::pref('font')?>, sans-serif;
	}
	
	<? endif; ?>
	
	</style>
	
	
	
	<script src="/javascripts/jquery.js"></script>
	<script src="/javascripts/miniwini.js"></script>
	
	<? if (Authly::signed()): ?>
	
	<script src="/javascripts/commently.js"></script>
	
	<? endif; ?>
	
	<!--[if (gte IE 6)&(lte IE 8)]>
	<script src="/javascripts/html5shiv.js"></script>
	<script src="/javascripts/selectivizr.js"></script>
	<![endif]-->
	
</head>

<body data-user="<?=(Authly::signed() ? 'y' : 'n')?>">
	
	<div id="wrapper">
	
		
	
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
				</ul>
			</nav>
		</header>
	

		<? if (Authly::signed()): ?>
	
		<div id="mybox">
		
			<a title="환경 설정" id="links-trigger" href="#" onclick="miniwini.links(this)"></a>
		
			<div id="links">
			
				<ul>
					<li><a href="<?=URL::to('dashboard')?>">대쉬보드</a></li>
					<li><a href="<?=URL::to('auth/edit')?>">환경 설정</a></li>
					<li><a href="<?=URL::to('auth/logout')?>">로그아웃</a></li>
				
				</ul>
			</div>
		
			<a title="메시지" id="messages-count" onclick="miniwini.messages(this)"></a><div id="messages"></div>
		
			<a title="알림" id="notifications-count" onclick="miniwini.notifications(this)"></a><div id="notifications"></div>
		
		</div>
	
		<? else: ?>
	
		<div id="guestbox">
	
			<ul>

				<li data-menu="login"><a href="<?=URL::to('auth/login')?>">로그인</a></li>
				<li data-menu="register"><a href="<?=URL::to('auth/register')?>">가입</a></li>

			</ul>

		</div>
	
		<? endif; ?>
		
	
		<div id="content">
		
			<? if (Session::has('errors')): ?>

			<div data-ui="error">
				
				<?=Session::get('errors')?>

			</div>

			<? endif; ?>

			<? if (Session::has('notification')): ?>

			<div data-ui="notification">
				
				<?=Session::get('notification')?>

			</div>

			<? endif; ?>
		
			<!--== Content ==-->

				<?=$content?>

			<!--== Content ==-->
		
		
		
		</div>
	
		<!--
		<footer>
			<a href="<?=URL::to('m')?>">(c)</a> miniwini / <?=$_SERVER['LARAVEL_ENV']?>
		
		</footer>
		-->
	
	
	
		<? if ( ! empty($visitors)): ?>
	
		<div id="visitors">
		
			<? if ( ! empty($visitors['users'])): ?>
		
			<ul>
			
				<? foreach ($visitors['users'] as $visitor): ?>

				<li id="connected-<?=$visitor->id?>">
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
		
	</div>

</body>
</html>