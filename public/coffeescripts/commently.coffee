class Commently
	constructor: ->
		@parser = new Showdown.converter()
	
	initialize: (form) ->
		@autocompleter = $('#commently-autocomplete')
		@textarea = $('.commently-form textarea')
		@checkbox_preview = $('.commently-form input[name=preview]')
		@checkbox_markdown = $('.commently-form input[name=format]')
		@label_preview = $('#label-preview')
		@previewbox = $('#commently-preview')
		@body = document.getElementById('commently-body')
		@userIndex = 1
		@bg = $('#commently-backgrounder')
		@root = @getRoot()
		
		if @isMarkdown()
			@label_preview.show()
		
		@textarea.bind('click', ->
			$(this).addClass('active');
			$(this).unbind('click');
		)
		
		@checkbox_preview.change( =>
			@textarea.unbind('keyup');
			return unless @isMarkdown()
			
			if @previewEnabled()

				@previewbox.show();
				@preview(@textarea.val());
				@textarea.bind('keyup', =>
					@preview(@textarea.val())
				)

			else
				@previewbox.hide();
				

			@textarea.addClass('active');
			@textarea.focus();
		)
		
		@checkbox_markdown.change( =>
			
			@previewbox[if @isMarkdown() and @previewEnabled() then "show" else "hide"]()
			@label_preview[if @isMarkdown() then "show" else "hide"]()
			
			@textarea.addClass('active');
			@textarea.focus();
		)
	
	isMarkdown: ->
		typeof @checkbox_markdown.attr('checked') != 'undefined'
	
	previewEnabled: ->
		typeof @checkbox_preview.attr('checked') != 'undefined'
	
	comment: (id) ->
		$('#commently-comment-' + id)
		
	log: (err) ->
		alert(err)

	getRoot: (url) ->
		$('[data-group=commently][data-type=comments][data-url='+url+']')

	reply: (id) ->
		article = @comment(id)
		root = @getRoot(article.data('url'))
		form = $('[data-type=form-container]', root)
		con = $('#commently-reply-' + id)
		if con.html()
			$('[data-type=form-wrapper]', root).append(form)
			article.removeClass('replying')
			$('input[name=parent_id]', form).val('')
		else
			con.append(form)
			article.addClass('replying')
			$('input[name=parent_id]', form).val(id)
			
		$('textarea', form).addClass('active').focus()
		
	preview: (src) ->
		$('#commently-preview').html(@parser.makeHtml(src))
	reset: (f) ->
		$('textarea', f).val('')
	
	keydown: (evt) ->
		try
			key = parseInt(evt.keyCode)
			if (key == 37 or key == 39) and @autocompleteCount()
				evt.preventDefault()
				evt.stopPropagation()

				return false
		catch err
			console.log(err)
			
	keypress: (evt) ->
		try
			key = parseInt(evt.keyCode)
			console.log("key : " + key)
			if key == 13 and @autocompleteCount()
				evt.preventDefault()
				evt.stopPropagation()
				@selectAutocomplete()
				return false
		catch err
			console.log(err)
		
	autocompleteCount: ->
		$('>div', @autocompleter).size()	
			
	autocomplete: (evt) ->

		sel = @body.selectionStart
		key = evt.keyCode
		
		buffer = []
		mark = false
		#@clearAutocomplete()
		console.log("=========" + key)
		
		if @autocompleter.html() and (key == 37 or key == 39)
			cnt = @autocompleteCount()
			if key == 39
				@userIndex += 1
				@userIndex = 1 if @userIndex > cnt
			else if key == 37
				@userIndex -= 1
				@userIndex = cnt if @userIndex < 1
				
			@activateAutocomplete(@userIndex)
					
			evt.preventDefault()
			evt.stopPropagation()
				
			return false
		
		
		for idx in [sel .. 0]
			chr = @body.value.charAt(idx)
			code = @body.value.charCodeAt(idx)

			if code == 32 or code == 10
				@clearAutocomplete()
				return
				
			if chr == '@' or chr == '/'
				mark = true
				break
			
			buffer.unshift(chr)
		
		if mark and buffer.length
			$.getJSON('/ajax/find_user?keyword=' + buffer.join(''), (data) =>
				@showAutocomplete(data) if data?
			)
		else
			@clearAutocomplete()
	
	selectAutocomplete: ->
		try
			user = $('>div:nth-child(' + @userIndex + ')', @autocompleter)
	#		alert(user.data('userid'))

			curPos = @body.selectionStart
			startPos = curPos
			discarded = 0

			for i in [curPos .. 0]

				chr = @body.value.charAt(i - 1)
				if chr == '@'
					marker = '@'
					break
				else if chr == '/'
					marker = '/'
					break

				startPos--
				discarded++

			strBefore = @body.value.substr(0,startPos)
			strAfter = @body.value.substr(startPos + discarded)


			@body.value = strBefore + user.data('name') + marker + strAfter
			@body.focus()
			#@textarea.val(user.data('userid'))
		catch err
		finally
			@clearAutocomplete()
			
		
	clearAutocomplete: ->
		@autocompleter.html('')
		@userIndex = 1
	
	activateAutocomplete: (idx) ->
		$('>div', @autocompleter).removeClass('active')
		user = $('>div:nth-child(' + idx + ')', @autocompleter)
		user.addClass('active')
	
	showAutocomplete: (data) ->
		h = []
		
		for user, idx in data.slice(0,5)
			h.push('<div '+(if idx == 0 then 'class="active"' else '')+' data-name="'+user.name+'" data-userid="'+user.userid+'"><img src="'+user.avatar_url+'" height="30"><div><strong>' + user.name+ '</strong><br>' + user.userid + '</div></div>')
		@autocompleter.html(h.join(''))
		
	edit: (id) ->
	
		article = @comment(id)
		root = @getRoot(article.data('url'))

		$.getJSON('/ajax/commently/' + id, (data) =>
			if ! data or ! data.id or parseInt(data.can_edit_within) <= 0
				alert('댓글을 수정할 수 없습니다.')
				return
			
			$('article', root).hide()
			time = Math.floor(data.can_edit_within)

			if time < 60
				remaining = time + '초'
			else
				remaining = Math.floor(time/60) + '분'

			form = $('[data-type=form-container]', root)
			con = $('#commently-reply-' + id)
			$('[data-type=form-wrapper]', root)
				.append(form)
				.prepend('<div class="commently-edit-title">댓글 수정하기 <small onclick="commently.cancelEdit('+id+')">[돌아가기]</small><span>수정 가능 시간이 <strong>' + remaining + '</strong> 남았습니다.</span></div>')

			$('input[name=id]', form).val(id)
			$('textarea', form).focus().val(data.body).addClass('active')
		)
		
		return false
	
	cancelEdit: (id) ->
		article = @comment(id)
		root = @getRoot(article.data('url'))
		form = $('[data-type=form-container]', root)
		$('article', root).show()
		$('.commently-edit-title', root).remove()
		$('input[name=id]', form).val('')
		$('textarea', form).val('')
	

	delete: (id) ->
		article = @comment(id)
		if $('.commently-deleting', article).size()
			article.removeClass('deleting')
			$('.commently-deleting', article).remove()
			return false 
			
		root = @getRoot(article.data('url'))
		article.addClass('deleting')
		$('[data-type=body]', article).after('<div class="commently-deleting"><a href="/commently/delete/'+id+'">댓글을 삭제하려면 클릭하세요.</a><small>삭제된 댓글은 되살릴 수 없습니다.</small></div>')
		
		return false

window.commently
$(->
	window.commently = new Commently()
)