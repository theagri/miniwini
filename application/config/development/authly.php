<?php
return array(
	'authly_key' => 'authly_key',
	
	'uniq_field' => 'userid',
	
	'table_user' => 'users',
	
	'table_connections' => 'user_connections',
	
	'roles' => array(
		0 => 'guest',
		1 => 'user',
		10 => 'admin',
		100 => 'superadmin',
		1000 => 'root'
	),
	
	'default_role_on_register' => 'user',
	
	'activate_on_register' => TRUE,
	
	'activation_method' => 'email',
	
	'roles' => array(
		1 => 'guest',
		2 => 'user',
		5 => 'moderator',
		10 => 'admin',
		100 => 'superadmin',
		1000 => 'root'
	),
	
	'validation_rules' => array(
		'userid' => 'required|min:4|alpha_dash',
		'email' => 'required|email',
		'password' => 'required|min:4'
	),

	'connections' => array(
		
		'enabled' => TRUE,
		
		'services' => array('twitter', 'facebook', 'google', 'linkedin', 'foursquare', 'openid'),
		
		'openid' => array(
			'host' => 'http://miniwini.dev',
			'required_fields' => array('namePerson/friendly', 'contact/email'),
			'optional_fields' => array(),
			'return_to' => 'http://miniwini.dev/auth/connected/openid'
		),
		
		'facebook' => array(
			'client_id' => '',
			'client_secret' => '',
			'redirect_uri' => 'http://miniwini.dev/auth/connected/facebook',
			'scope' => 'email,read_stream,offline_access,publish_stream,user_about_me,friends_about_me,user_website,friends_website,user_about_me,user_relationship_details,friends_relationship_details,user_activities,friends_activities,user_interests,friends_interests,user_likes,user_photo_video_tags,user_photos,friends_photos,user_videos,read_mailbox,read_friendlists,manage_friendlists,user_checkins,friends_checkins,read_requests,user_status,friends_status,user_online_presence,friends_online_presence,user_notes,friends_notes,user_location,friends_location'
		),
	
		'google' => array(
			'return_to' => 'http://miniwini.dev/auth/connected/google',
		),
	
		'foursquare' => array(
			'client_id' => '',
			'client_secret' => '',
			'redirect_uri' => 'http://miniwini.dev/auth/connected/foursquare'
		),
	
		'twitter' => array(
			'consumer_key' => '',
			'consumer_secret' => '',
			'oauth_callback' => 'http://miniwini.dev/auth/connected/twitter'
		),
	
		'linkedin' => array(
			'consumer_key' => '',
			'consumer_secret' => '',
			'oauth_callback' => 'http://miniwini.dev/auth/connected/linkedin'
		),
	),
	
	'reserved_userids' => 'about,account,accounts,activities,activity,admin,administrator,administrators,ajax,all,announcements,api,app,apps,asset,assets,auth,bbs,biz,blog,board,business,comment,comments,config,configurations,contact,contacts,css,dashboard,dev,developer,developers,device,devices,docs,download,downloads,error,errors,explore,extra,extras,faq,favorites,features,font,fonts,forum,forums,friends,goodies,help,home,inbox,invitations,invite,issue,issues,javascript,javascripts,jobs,js,list,login,logout,me,message,messages,misc,my,notification,notifications,oauth,page,pages,place,places,privacy,product,products,profile,register,roadmap,roadmaps,rules,script,scripts,search,sent,setting,settings,share,signin,signup,statistics,stats,stylesheet,stylesheets,support,unregister,user,users,welcome,widget,widgetsts'
);