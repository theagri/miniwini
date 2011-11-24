class Commently
	constructor: ->

	log: (err) ->
		alert(err)

	getRoot: (url) ->
		$('[data-group=commently][data-type=comments][data-url='+url+']')

	reply: (id) ->
		article = $('#commently-comment-' + id)
		root = @getRoot(article.data('url'))
		form = jQuery('[data-type=form-container]', root)
		$('#commently-reply-' + id).append(form)
		$('textarea', form).addClass('active').focus()
		$('input[name=parent_id]', form).val(id)
		
	reset: (f) ->
		$('textarea', f).val('')
		
window.commently
$(->
	window.commently = new Commently()
)