class Commently
	constructor: ->
		@parser = new Showdown.converter()
	
	initialize: (form) ->
		@textarea = $('.commently-form textarea')
		@checkbox_preview = $('.commently-form input[name=preview]')
		@checkbox_markdown = $('.commently-form input[name=format]')
		@label_preview = $('#label-preview')
		@previewbox = $('#commently-preview')
		
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
				);

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
	
	log: (err) ->
		alert(err)

	getRoot: (url) ->
		$('[data-group=commently][data-type=comments][data-url='+url+']')

	reply: (id) ->
		article = $('#commently-comment-' + id)
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
		
window.commently
$(->
	window.commently = new Commently()
)