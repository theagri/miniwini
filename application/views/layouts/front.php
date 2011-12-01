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
	
	<? if ( ! empty($_COOKIE['x'])): ?>
	
	<style type="text/css">
	#wrapper
	{
		left:<?=$_COOKIE['x']?>px;
	}
	</style>
	
	<? endif; ?>

	
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

		
	</header>
	
	<aside id="pane-left">
		
		<div class="mover"></div>
		
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
		
		<? endif; ?>
		
		<ul id="guest-menu">
			
			<? if (Authly::signed()): ?>
			
			<? else: ?>

			<li data-menu="login"><a href="<?=URL::to('auth/login')?>">로그인</a></li>
			<li data-menu="register"><a href="<?=URL::to('auth/register')?>">회원 가입</a></li>

			<? endif; ?>

		</ul>
		
		
		
	</aside>
	<div id="content">
		
		<div id="content-box">
			
			
			<nav>
				<ul>
					
					<li><a href="<?=URL::to('board/talk')?>">자유게시판</a></li>
					<li><a href="<?=URL::to('board/share')?>">알짜게시판</a></li>
					<li><a href="<?=URL::to('board/qna')?>">질문&amp;답변</a></li>

				</ul>

			</nav>
			
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
		
		
		<footer>
			(c) miniwini / <?=$_SERVER['LARAVEL_ENV']?> 
			
		</footer>
		
	</div>
	
	
	
	
	
	<aside id="pane-right">
		
		<div class="mover"></div>
		
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
		
	</aside>
</div>

</body>
</html>