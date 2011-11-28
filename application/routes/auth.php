<?php
return array(
	
	/*
	 * =====================================================================
	 *
	 * 			Login/Logout
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /auth/login' => array('name' => 'login', function(){
		if ( ! Config::get('authly.register_enabled')) return Response::error(500);
		
		Title::put('로그인');
		return View::of_front()->nest('content', 'auth/login', array(
			'error' => Session::get('login_error'),
			'back_to' => Session::get('back_to'),
			'message' => Session::get('message')
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'POST /auth/login' => array('before' => 'csrf', function(){
		if ( ! Config::get('authly.register_enabled')) return Response::error(500);
		
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
		
		return Redirect::to_login()->with('errors', 'Failed');
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
		return View::of_front()->nest('content', 'auth/edit');
	}),
	
	// ---------------------------------------------------------------------
	
	'PUT /auth/edit' => array('before' => 'signed, csrf', function(){

		$val = Validator::make(array('name' => Input::get('name')), array('name' => 'required|min:' . Config::get('miniwini.user.min_name_size') . '|max:' . Config::get('miniwini.user.max_name_size')));
		
		if ($val->valid())
		{
			$input = array(
				'name' => strip_tags(Input::get('name')),
				
			);
			$file = Input::file('avatar');
			
			$val_file = Validator::make(array('avatar' => $file), array('avatar' => 'image|max:' . Config::get('miniwini.avatar.max_size')));

			if ( ! empty($file) and ! empty($file['tmp_name']) and $val_file->valid())
			{
				$img = Image_util::make($file['tmp_name']);
				$savepath = PUBLIC_PATH . '/assets/avatars/avatar_' . Authly::get_userid() . '.png';
				$img->resize(60, $savepath);
				$input['avatar_url'] = Config::get('application.url') . '/assets/avatars/avatar_' . Authly::get_userid() . '.png';
			}
			
			
			Authly::update($input);
			return Redirect::to('auth/edit')->with('notification', 'Updated!');
		}
		
		return Redirect::to('auth/edit');
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			password
	 *
	 * =====================================================================
	 */
	
	'PUT /auth/change_password' => array('before' => 'signed, csrf', function(){
	
	
		if ( ! Authly::check_password(Input::get('password_current')))
		{
			return Redirect::to('auth/edit')->with('errors', '현재 비밀번호가 맞지 않습니다.');
		}
		
		$val = Validator::make(Input::all(),array(
			'password_current' => 'required',
			'password' => 'required|confirmed',
		));
		
		if ($val->valid() and Authly::change_password(Input::get('password')))
		{
			return Redirect::to('auth/edit')->with('notification', '비밀번호가 변경되었습니다.');
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
		if ( ! Config::get('authly.register_enabled')) return Response::error(500);
		
		Title::put('가입');
		return View::of_front()->nest('content', 'auth/register', array(
			'error' => Session::get('error')
		));
	}),
	
	// ---------------------------------------------------------------------

	'POST /auth/register' => function(){
		if ( ! Config::get('authly.register_enabled')) return Response::error(500);
		
		$uniq_val = Input::get(Config::get('authly.uniq_field'));
		
		$input = array(
				'userid' => Input::get('userid'),
				'email' => Input::get('email'),
				'name'	=> Input::get('name'),
				'password' => Input::get('password'),
				'avatar_url' => Config::get('miniwini.avatar.no_avatar_url')
			);

		if (Authly::exists($uniq_val))
		{
			return Redirect::to_register()->with('errors', 'Already taken');
		}
		
		if (Authly::is_reserved($uniq_val))
		{
			return Redirect::to_register()->with('errors', 'Reserved');
		}
		
		if ( ! Authly::validate($input))
		{
			return Redirect::to_register();
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
		
		return Redirect::to_register()->with('errors', 'Unknown error');
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
			return View::of_front()->nest('content', 'auth/activation_email_sent', array(
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
		if ( ! Config::get('authly.connections.enabled')) return Response::error(500);
		
		return View::of_front()->nest('content', 'auth/connections');
	},
	
	
	// ---------------------------------------------------------------------
	
	'GET /auth/connect/(:any)' => function($service){
		if ( ! Config::get('authly.connections.enabled')) return Response::error(500);
		
		return Redirect::to($url);
	},
	
	// ---------------------------------------------------------------------

	'POST /auth/connect/(:any)' => function($service){
		if ( ! Config::get('authly.connections.enabled')) return Response::error(500);

		$url = Authly::connect($service, Input::get('openid_identifier'));
		return Redirect::to($url);
	},

	// ---------------------------------------------------------------------
	
	'GET /auth/connected/(:any)' => function($service){
		if ( ! Config::get('authly.connections.enabled')) return Response::error(500);
		
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