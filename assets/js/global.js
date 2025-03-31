let isModified = false;

function init(){

	for( const input of document.querySelectorAll('input') ) {
		input.addEventListener('change', function(){
			isModified = true;
			handleSubmitButton();
		});
		input.addEventListener('input', function(){
			isModified = true;
			handleSubmitButton();
		});
	}

	for( const select of document.querySelectorAll('select') ) {
		if( select.id === 'sort-order' ) continue;
		select.addEventListener('change', function(){
			isModified = true;
			handleSubmitButton();
		});
	}

	const form = document.querySelector('form');
	form.addEventListener('submit', function(){
		isModified = false;
	});

	handleSubmitButton();

}	
document.addEventListener( 'DOMContentLoaded', init, false );


function handleSubmitButton() {

	const submitButton = document.getElementById('submit-button');
	if( ! submitButton ) return;

	let readyToSend = true;

	for( const input of document.querySelectorAll('input') ) {
		if( ! input.value ) readyToSend = false;
	}

	for( const select of document.querySelectorAll('select') ) {
		if( select.id === 'sort-order' ) continue;
		
		if( ! select.value ) readyToSend = false;
	}

	// TODO: check if priority prerequisites are met

	submitButton.disabled = ! readyToSend;

}


window.addEventListener('beforeunload', function(event){
	if( ! isModified ) return;
	event.preventDefault();
	event.returnValue = ''; // needed for some browsers
});
