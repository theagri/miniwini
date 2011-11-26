
		
class Miniwini
	constructor: ->

		@notificationCheckInterval = 10000
		@checkNotification()
	
	checkNotification: ->
		try
			$elem = $('#notifications-count')
			$.getJSON('/notification/count', (data) =>

					if data and data.count > 0
						$elem.data('time', data.last_updated_at.toString()).html(data.count).show()
					else
						$elem.hide()
				
					window.setTimeout(=>
						@checkNotification()
					, @notificationCheckInterval)
			)
		catch err
			
	notifications: ->
		try
			$elem = $('#notifications')
			
			$(document).unbind('click')
			
			$(document).bind('click', (evt) =>
				if $elem.css('display') != 'none' and evt.target.id != 'notifications-count'
					$elem.hide()
			)
			
			if $('#notifications-count').data('time') == $elem.data('time')
				window.setTimeout(=>
					$elem.toggle()
				,10)
			else
				$.getJSON('/notification/all', (data) =>
					html = []
					$.each(data, (idx, noti) =>
				
						switch noti.action
							when "comment_on_topic"
								time = $.timeago(new Date(noti.created_at * 1000))
								h = "<div data-url=\"#{noti.url}\" data-time=\"#{noti.created_at}\"><figure data-type=\"avatar-medium\"><img src=\"#{noti.actor_avatar}\" alt=\"#{noti.actor_name}\"></figure><p>#{noti.actor_name}님이 당신의 게시물에 댓글을 남겼습니다. <q>#{noti.body}</q><time>#{time}</time></p></div>"
					
							when "comment_on_comment"
								time = $.timeago(new Date(noti.created_at * 1000))
								h = "<div data-url=\"#{noti.url}\" data-time=\"#{noti.created_at}\"><figure data-type=\"avatar-medium\"><img src=\"#{noti.actor_avatar}\" alt=\"#{noti.actor_name}\"></figure><p>#{noti.actor_name}님이 당신의 댓글에 댓글을 남겼습니다. <q>#{noti.body}</q><time>#{time}</time></p></div>"
					
						$('#notifications').data('time', noti.created_at.toString()) if idx == 0
						html.push(h)
					)

					$elem.html(html.join('')).toggle()
					$('#notifications  div[data-url]').click(->
						time = $(this).data('time')
						url = $(this).data('url')
				
						$.ajax({
							url:'/notification/read?time=' + time
							success: (res) ->
								document.location.href = url
						})
					)
				)
			
		catch err
	

	saveToDraft: (f) ->
		f.elements['state'].value = 'draft';
		f.submit()
		
window.miniwini
$(->
	window.miniwini = new Miniwini()
)