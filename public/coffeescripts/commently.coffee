class Commently
	constructor: ->
		@apiURL = 'http://miniwini.dev/commently'
		@initialize()

	log: (err) ->
		alert(err)
		
	setURL: (url) ->
		@apiURL = url
		
	initialize: ->

		$.each(jQuery('[data-group=commently][data-type=comments]'), (idx, container) =>
			container = $(container)
			@comments(container.data('url'), container)
		)
		
	getRoot: (url) ->
		$('[data-group=commently][data-type=comments][data-url='+url+']')
		
	comments: (url, target) ->
		if typeof target == 'string'
			target = $(target)
		
		$.ajax({
			url: @apiURL
			type: 'GET'
			data : 'url=' + encodeURIComponent(url)
			
			beforeSend: (xhr, setting) ->

			success: (result, textStatus, xhr) =>
				comments = JSON.parse(result)
				@draw(comments.data, target)
				@drawForm(comments.form, target)
			error: (xhr, textStatus, error) ->
				alert('error : ' + error)
			complete: (xhr, status) ->
				#alert('complete : ' + status)
		})
	
	draw: (comments, target) ->
		
		try
			root = $('<div>')

			for c in comments
				
				if c.parent_id
					parent = jQuery('#commently-comment-' + c.parent_id, root)
					parent_depth = parent.attr('class')
					depth = if parent_depth? then parseInt(parent_depth.replace(/^depth-/, '')) + 1 else 1
					child = $(c.html).addClass('depth-' + depth)
					parent.after(child)
				else
					root.append(c.html)

			target.append($('> article', root))
			
		catch err
			@log(err)
			
	drawForm: (form, target) ->

		target.append(form)
		f = $('form', target)
		f.bind('submit', (evt) =>
			f = $(evt.target)
			
			evt.stopPropagation()
			evt.preventDefault()			
			
			$.ajax({
				url: f.attr('action')
				type: 'POST'
				data: f.serialize()
				beforeSend: (xhr, setting) =>
					@makeBusy(f, true)
					
				success: (result, textStatus, xhr) ->
					url = $(result).data('url')
					target.append(result)
					
				error: (xhr, textStatus, error) ->
					@makeBusy(f, false)
					alert('error')
					
				complete: (xhr, status) =>
					@makeBusy(f, false)
					@reset(f)
			})
			return false
		)
		
	reply: (id) ->
		article = jQuery('#commently-comment-' + id)
		root = @getRoot(article.data('url'))
		form = jQuery('[data-type=form-container]', root)
		$('#commently-reply-' + id).append(form)
		$('textarea', form).trigger('focus')
		$('input[name=parent_id]', form).val(id)
			
	makeBusy: (f, busy) ->
		
		if busy
			$('input[type=submit]', f).val('wait...').attr('disabled', true)
			$('textarea', f).attr('disabled', true)
		else
			$('input[type=submit]', f).val('submit').attr('disabled', false)
			$('textarea', f).attr('disabled', false)
			
	reset: (f) ->
		$('textarea', f).val('')
		
window.commently
$(->
	window.commently = new Commently()
)