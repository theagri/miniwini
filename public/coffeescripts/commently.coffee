class Commently
	constructor: ->

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
		
		
	reset: (f) ->
		$('textarea', f).val('')
		
window.commently
$(->
	window.commently = new Commently()
)