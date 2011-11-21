class Miniwini
	constuctor: ->
		
	saveToDraft: (f) ->
		f.elements['state'].value = 'draft';
		f.submit()
		
window.miniwini
$(->
	window.miniwini = new Miniwini()
)