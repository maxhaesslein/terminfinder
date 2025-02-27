(function(){

	var isModified = false;

	function init(){

		for( var input of document.querySelectorAll('input') ) {
			input.addEventListener('change', function(){
				isModified = true;
			});
		}

		var form = document.querySelector('form');
		form.addEventListener('submit', function(){
			isModified = false;
		});

	}	
	document.addEventListener( 'DOMContentLoaded', init, false );



	window.addEventListener('beforeunload', function(event){
		if( ! isModified ) return;
		event.preventDefault();
		event.returnValue = ''; // needed for some browsers
	});


})();