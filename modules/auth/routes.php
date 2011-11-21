<?php
return array(
	
	// ---------------------------------------------------------------------
	
	'GET /auth/login' => array('name' => 'login', function(){
		Title::put('로그인');
		return View::of_front()->partial('content', 'auth/login', array(
			'error' => Session::get('login_error'),
			'back_to' => Session::get('back_to'),
			'message' => Session::get('message')
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'POST /auth/login' => array('before' => 'csrf', function(){

		$field = Config::get('authly.uniq_field');
		$res = Authly::sign_in(Input::get($field), Input::get('password'));
		if ($res)
		{
			$back_to = Input::get('back_to');
			if ($back_to)
			{
				return Redirect::to($back_to);
			}
			
			return Redirect::to_home();
		}
		
		return Redirect::to_login()->with('login_error', 'Failed');
	}),
		
	// ---------------------------------------------------------------------
	
	'GET /auth/logout' => function(){
		Authly::sign_out();
		return Redirect::to_home();
	},
	
	/*
	 * =====================================================================
	 *
	 * 			Edit
	 *
	 * =====================================================================
	 */
	

	// ---------------------------------------------------------------------
	
	'GET /auth/edit' => array('before' => 'signed', function(){
		Title::put('정보 수정');
		return View::of_front()->partial('content', 'auth/edit');
	}),
	
	// ---------------------------------------------------------------------
	
	'PUT /auth/edit' => array('before' => 'csrf, signed', function(){
		
		$input = array(
			'name' => Input::get('name'),
			'avatar_url' => Input::get('avatar_url')
		);
		
		$val = Validator::make($input, array(
			'name' => 'required',
			'avatar_url' => 'url'
		));
		
		if ($val->valid())
		{
			Authly::update($input);
			return Redirect::to('auth/edit')->with('notification', 'Updated!');
		}
		
		return Redirect::to('auth/edit');
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			Register
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /auth/register' => array('name' => 'register', 'do' => function(){
		Title::put('가입');
		return View::of_front()->partial('content', 'auth/register', array(
			'error' => Session::get('error')
		));
	}),
	
	// ---------------------------------------------------------------------

	'POST /auth/register' => function(){
		$uniq_val = Input::get(Config::get('authly.uniq_field'));
		
		$input = array(
				'userid' => Input::get('userid'),
				'email' => Input::get('email'),
				'name'	=> Input::get('name'),
				'password' => Input::get('password')
			);

		if (Authly::exists($uniq_val))
		{
			return Redirect::to_register()->with('error', 'Already taken');
		}
		
		if (Authly::is_reserved($uniq_val))
		{
			return Redirect::to_register()->with('error', 'Reserved');
		}
		
		if (($val = Authly::validate($input)) !== TRUE)
		{
			return Redirect::to_register()->with('error', 'Invalid');
		}
		
		$res = Authly::register($input);

		if ($res)
		{
			if (Config::get('authly.activate_on_register') === TRUE)
			{
				$signed = Authly::sign_in(Input::get('userid'), Input::get('password'));
				return Redirect::to_home();
			}
			else
			{
				return Redirect::to('auth/waiting_activation')->with('activation', array(
					'email' => $input['email'],
					'code' => Session::get('activation_code')
				));
			}
		}
		
		return Redirect::to_register()->with('error', 'Unknown error');
	},
	
	// ---------------------------------------------------------------------
	
	'GET /auth/waiting_activation' => array('needs' => 'swiftmailer', 'do' => function(){
		$activation = Session::get('activation');
		$code = $activation['code'];
		$email = $activation['email'];
		$body = <<<EMAIL
	Activate your account : <a href="http://miniwini.dev/auth/activate?code={$code}">{$code}</a>	
EMAIL;

		$transport = Swift_MailTransport::newInstance();
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance('Miniwini activation required')
			->setFrom(array('mywizz@miniwini.com' => 'Miniwini'))
			->setTo($email)
			->setBody($body, 'text/html');

		$result = $mailer->send($message);
		if ($result)
		{
			Title::put('인증 메일 발송 결과');
			return View::of_front()->partial('content', 'auth/activation_email_sent', array(
				'email' => $email
			));
		}
		
		return Response::error(500);
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /auth/activate' => function(){
		$code = Input::get('code');
		
		if ( ! $code)
		{
			return Reeponse::error(500);
		}
		
		$activated = Authly::activate_by_code($code);
		if ($activated)
		{
			return Redirect::to_login();
		}
		
		return Response::error(500);
	},
	
	
	/*
	 * =====================================================================
	 *
	 * 			Connections
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /auth/connections' => function(){
		if ( ! Config::get('authly.connectiones.enabled')) return Response::error(500);
		
		return View::of_front()->partial('content', 'auth/connections');
	},
	
	
	// ---------------------------------------------------------------------
	
	'* /auth/connect/(:any)' => function($service){
		if ( ! Config::get('authly.connectiones.enabled')) return Response::error(500);
		
		$url = Authly::connect($service, Input::get('openid_identifier'));
		return Redirect::to($url);
	},

	// ---------------------------------------------------------------------
	
	'GET /auth/connected/(:any)' => function($service){
		if ( ! Config::get('authly.connectiones.enabled')) return Response::error(500);
		
		$available_services = Config::get('authly.connections.services');
		
		if ( ! in_array($service, $available_services))
		{
			return Response::error(500);
		}
		
		switch ($service)
		{
			case 'twitter':
			case 'linkedin':
			case 'facebook':
			case 'foursquare':
			case 'google':
			case 'openid':
				Authly::connected($service, Input::get());
				return Redirect::to_dashboard();
		}
	}
);