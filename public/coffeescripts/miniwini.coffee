class Miniwini
	constructor: ->
		
		@doc = $(document)
		
		@notificationCheckInterval = 7000
		@checkNotification()
		@noti_count = $('#notifications-count')
		@noti_list = $('#notifications')
		@links_trigger = $('#links-trigger')
		@links_list = $('#links')
		@context = $('#user-context')
		@contextTimer
		@tpl = {
			context:$.template('#tpl-user-context')
		}
	
		$('#wrapper').css({left:parseInt($.cookie('x'))})
		
		$('#wrapper').prepend('<div class="mover" id="mover-left"></div>').prepend('<div class="mover" id="mover-right"></div>')
		
		$('#wrapper').draggable({
			handle:'.mover'
			axis:'x'
			containment: [0,0,(window.innerWidth - 1056), 0]
			stop: =>

				x = $('#wrapper').offset().left
				$.cookie('x', x, {
					expires:365
					path:'/'
				})
		})
		
		# context
		self = this
		@context.mouseleave(=>@hideContext())
		$('figure[data-type^=avatar-]').mouseenter( ->
			t = $(this)
			return if not t.data('userid')
			c = self.context.hide()
			p = t.offset()
			c.html($.tmpl(self.tpl.context, t.data()))

			top = p.top - c.height()
		
			c.css({
				left:p.left + t.width()/2 - c.width()/2
				top:top - 20
				opacity:0
				zIndex:-1
			})
		
			self.contextTimer = window.setTimeout(=>
				c.show()
				window.setTimeout(=>
					c.css({top:Math.max(0, top-10),opacity:1,zIndex:9999999})
				,1)
				
			, 600)
		).mouseleave(=>
			window.clearTimeout(@contextTimer)
		)
		
		
		# exp
		if @logged()
			exp = $('[data-ui=exp]')

			if exp.size()
				exp.css({
					top:(window.innerHeight - 100) / 2
				}).show()
					
				window.setTimeout( ->
					exp.addClass('active')
				,400)
		
		
		@doc.bind('click', (evt) =>
			@handleClick(evt)
		)
		
		@doc.bind('keyup', (evt) =>
			@handleHotkey(evt)
		)
	hideContext: ->
		window.clearTimeout(@contextTimer)
		@context.css({opacity:0,zIndex:-1}).hide()
		
	handleHotkey: (evt) ->

		return if ['html', 'body'].indexOf(evt.target.nodeName.toLowerCase()) == -1
		
		code = evt.which || evt.keyCode
		char = String.fromCharCode(code);
		switch char
			when "Q"

				return unless @logged()
				if @noti_list.css('display') != 'none' then $('>div:last-child', @noti_list).trigger('click') else @notifications()
				
		
	handleClick: (evt) ->
		@hideContext()
		if evt.target.id is 'links-trigger'
			@noti_list.hide()
			@noti_count.removeClass('opened')
			return
		if evt.target.id is 'notifications-count' or evt.target.id is 'notifications-count-data'
			@links_list.hide()
			@links_trigger.removeClass('opened')
			return
		@closeAll()
		
	closeAll: ->
		if @noti_count.hasClass('opened')
			@noti_list.hide()
			@noti_count.removeClass('opened')
			
		if @links_trigger.hasClass('opened')
			@links_list.hide()
			@links_trigger.removeClass('opened') 
		
	logged: ->
		$('body').data('user') is 'y'
		
	checkNotification: ->
		
		try
			
			return unless @logged()
			
			$.getJSON('/notification/count', (data) =>
					
					changed = false
					if data and data.count > 0
						changed = (data.count != @noti_count.html())
						document.title = '(' + data.count + ') ' + document.title.replace(/^\(\d+\) /, '')
						@noti_count.data('time', data.last_updated_at.toString()).data('count', data.count).html('<span id="notifications-count-data">' + data.count + '</span>').addClass('active')
					else
						document.title = document.title.replace(/^\(\d+\)$ /, '')
						
					
					window.setTimeout(=>
						@checkNotification()
					, @notificationCheckInterval)
			)
		catch err
			
	notifications: ->
		
		try
			return unless @logged()
			
			unless @noti_count.data('count')
				location.href = '/notifications'
				return
			
			if @noti_count.data('time') == @noti_list.data('time')
				@noti_list.toggle()
				@noti_count[if @noti_list.css('display') != 'none' then 'addClass' else 'removeClass']('opened')

			
			else
				if @noti_count.data('loading') == 'y'
					return
					
					
				@noti_count.addClass('loading')
				@noti_count.data('loading', 'y')
				$.getJSON('/notification/all', (data) =>
					@noti_count.removeClass('loading')
					@noti_count.data('loading', 'n')
					html = []
					$.each(data, (idx, noti) =>
						time = $.timeago(new Date(noti.created_at * 1000))
						noti.body = noti.body.replace(/(@|\/)(.+?)\1/, '$2')
						switch noti.action
							when "comment_on_topic"
								body = "<p>#{noti.actor_name}님이 당신의 게시물에 댓글을 남겼습니다. <q>#{noti.body}</q>"
								
							when "comment_and_mention_on_topic"
								body = "<p>#{noti.actor_name}님이 당신을 언급하면서 당신의 게시물에 댓글을 남겼습니다. <q>#{noti.body}</q>"
																	
							when "comment_on_comment"
								body = "<p>#{noti.actor_name}님이 당신의 댓글에 댓글을 남겼습니다. <q>#{noti.body}</q>"
								
							when "comment_and_mention_on_comment"
								body = "<p>#{noti.actor_name}님이 당신을 언급하면서 당신의 댓글에 댓글을 남겼습니다. <q>#{noti.body}</q>"
								
							when "mention"
								body = "<p>#{noti.actor_name}님이 당신을 언급했습니다. <q>#{noti.body}</q>"
								
						@noti_list.data('time', noti.created_at.toString()) if idx == 0
						
						h = "<div data-url=\"#{noti.url}\" data-time=\"#{noti.created_at}\"><figure data-type=\"avatar-medium\"><img src=\"#{noti.actor_avatar}\" alt=\"#{noti.actor_name}\"></figure>#{body}<time>#{time}</time></p></div>"
						html.push(h)
					)

					@noti_list.html(html.join('')).toggle()
					$('#notifications  div[data-url]').click(->
						
						time = $(this).data('time')
						url = $(this).data('url')
						
						$.ajax({
							url:'/notification/read?time=' + time
							success: (res) ->
								document.location.href = url
						})
					)
					
					@noti_count[if @noti_list.css('display') != 'none' then 'addClass' else 'removeClass']('opened')
				)
				
				
			
		catch err
	
	messages: (src) ->
		
	links: (src) ->
		return unless @logged()
		@links_list.toggle()
		@links_trigger[if @links_list.css('display') != 'none' then 'addClass' else 'removeClass']('opened')
		
	submitPost: (f) ->
		$('#submitButton').attr('disabled', true);
		return true;

	saveToDraft: (f) ->
		f.elements['state'].value = 'draft';
		f.submit()
		
	selectTab: (tab) ->
		try
			type = $(tab).data('tab')
			panel = $('[data-ui=tabbed-panel]')

			$('[data-tab]', panel).removeClass('active')
			$('[data-tab='+type+']', panel).addClass('active')

			$('[id^=panel-]', panel).hide()
			$('[id=panel-' + type + ']', panel).show()
			
		catch err
	
		
	setPostType: (tab) ->
		@selectTab(tab)
		type = $(tab).data('tab')

		if type == 'post-type-preview'

			$('#preview-body').html('')

			if ($('#format').val()) == 'markdown'

				body = $('#body').val()
				parser = new Showdown.converter()
				$('#preview-body').html(parser.makeHtml(body))
			else
				$.ajax({
					url: '/ajax/preview',
					type: 'POST',
					data: {
						body: $('#body').val()
					}
					success: (html) =>
						$('#preview-body').html(html)
				})
				

		$('#preview-section')[if type == 'post-type-preview' then 'show' else 'hide']()
		$('#common-controls')[if type == 'post-type-preview' then 'hide' else 'show']()
	
	uploadPhoto: (form) ->		
		return false unless form.elements['photo'].value

		$('input[type=submit]', form).attr('disabled', true).hide()
		$('#upload-waiting').addClass('active')
		$('input[type=file]', form).hide()
		return true
		
	loadRecentPhoto: ->
		try
			return unless localStorage or localStorage.uploadedPhoto
			photos = JSON.parse(localStorage.uploadedPhoto)
			if photos
				con = $('#uploaded-photos')
				tpl = $('#tpl-uploaded-photo').template()
				for photo in photos
					if $('img[src="' + photo.url + '"]', con).size() == 0
						con.append($.tmpl(tpl, photo).html())
		catch err
			
	selectPhoto: (photo) ->
		img = $('img', photo)
		body = document.getElementById('body')
		src = img.attr('src')
		body.focus()
		startPos = body.selectionStart
		endPos = body.selectionEnd
		src = '![](' + src + ')' if $('#format').val() is 'markdown'
		$(photo).addClass('selected')
		body.value = body.value.substring(0, startPos)+ src + "\n" + body.value.substring(endPos, body.value.length)
		
		
	photoUploadFailed: ->
		alert('업로드 실패')
		
	photoUploaded: (res) ->
		if res and res.url
			tpl = $('#tpl-uploaded-photo').template()
			$('#uploaded-photos').prepend($.tmpl(tpl, res).html())
			if localStorage?
				photos = if localStorage.uploadedPhoto? then JSON.parse(localStorage.uploadedPhoto) else []
				photos.unshift(res)
				localStorage.uploadedPhoto = JSON.stringify(photos.slice(0,10))
		
window.miniwini
$(->
	window.miniwini = new Miniwini()
)