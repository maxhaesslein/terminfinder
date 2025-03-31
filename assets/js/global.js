let isModified = false;

function init(){

	for( const input of document.querySelectorAll('input') ) {
		input.addEventListener('change', function(){
			isModified = true;
		});
		input.addEventListener('input', function(){
			isModified = true;
		});
	}

	for( const select of document.querySelectorAll('select') ) {
		if( select.id === 'sort-order' ) continue;
		select.addEventListener('change', function(){
			isModified = true;
		});
	}

	const form = document.querySelector('form');
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
