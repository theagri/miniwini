class Commently
	constructor: ->

		
	init: ->
		$('.commently-form').bind('submit', (evt) =>
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
					$('div[data-page-url="'+url+'"]').append(result)
					
				error: (xhr, textStatus, error) ->
					
				complete: (xhr, status) =>
					@makeBusy(f, false)
			})
			return false
		)
	
	makeBusy: (f, busy) ->
		
		if busy
			$('input[type=submit]', f).val('wait...').attr('disabled', true)
		else
			$('input[type=submit]', f).val('submit').attr('disabled', false)
		

$(->
	commently = new Commently()
	commently.init()
)