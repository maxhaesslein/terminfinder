(function(){

	let isModified = false;

	function init(){

		updateLineCounts();

		for( const input of document.querySelectorAll('input') ) {
			input.addEventListener('change', function(){
				isModified = true;
			});
		}

		for( const select of document.querySelectorAll('select') ) {
			select.addEventListener('change', function(){
				isModified = true;
				updateLineCounts();
			});
		}

		const form = document.querySelector('form');
		form.addEventListener('submit', function(){
			isModified = false;
		});

		const addEventButton = document.getElementById('add-event');
		if( addEventButton ) {
			addEventButton.addEventListener('click', addEvent);
		}

		handleDragDrop();

	}	
	document.addEventListener( 'DOMContentLoaded', init, false );


	function handleDragDrop(){
		const table = document.getElementById('new-table');
		if( ! table ) return;

		const tableBody = table.querySelector('tbody');

		let draggedRow = null;

		tableBody.addEventListener("dragstart", (event) => {
			draggedRow = event.target;
			event.target.classList.add("dragging");
		});

		tableBody.addEventListener("dragover", (event) => {
			event.preventDefault();
			const targetRow = event.target.closest("tr");
			if (targetRow && targetRow !== draggedRow) {
				const bounding = targetRow.getBoundingClientRect();
				const offset = event.clientY - bounding.top;
				if (offset > bounding.height / 2) {
					targetRow.after(draggedRow);
				} else {
					targetRow.before(draggedRow);
				}
			}
		});

		tableBody.addEventListener("dragend", () => {
			draggedRow.classList.remove("dragging");
			draggedRow = null;
		});
	}


	function addEvent(e) {
		const table = document.getElementById('new-table');
		if( ! table ) return;

		e.preventDefault();

		isModified = true;

		const newTr = document.getElementById('new-event-template').cloneNode(true);
		newTr.removeAttribute('id');
		newTr.removeAttribute('hidden');
		newTr.querySelector('button').addEventListener('click', removeEvent);
		newTr.querySelector('input').required = true;
		newTr.querySelector('select').required = true;

		table.querySelector('tbody').appendChild(newTr);

		newTr.querySelector('input').focus();
	}


	function removeEvent(e){
		e.preventDefault();

		const tr = this.closest('tr');

		tr.remove();
	}


	function updateLineCounts() {
		const table = document.getElementById('schedule-list');

		if( ! table ) return;

		const trs = table.querySelectorAll('tr.event-line');

		let people_count = parseInt(table.dataset.peopleCount, 10);

		let max_yes = 0,
			max_no = 0,
			max_yes_maybe = 0;

		for( const tr of trs ) {

			let yes = parseInt(tr.dataset.yes, 10),
				no = parseInt(tr.dataset.no, 10),
				maybe = parseInt(tr.dataset.maybe, 10);

			if( tr.querySelector('select') ) {
				const val = parseInt(tr.querySelector('select').value, 10);
				if( val === 0 ) {
					no++;
				} else if( val === 1 ) {
					yes++;
				} else if( val === 2 ) {
					maybe++;
				}
			}

			if( yes > max_yes ) max_yes = yes;
			if( no > max_no ) max_no = no;
			if( yes+maybe > max_yes_maybe ) max_yes_maybe = yes+maybe;
			
		}

		for( const tr of trs ) {
			let yes = parseInt(tr.dataset.yes, 10),
				no = parseInt(tr.dataset.no, 10),
				maybe = parseInt(tr.dataset.maybe, 10),
				count = people_count;

			if( tr.querySelector('select') ) {
				const val = parseInt(tr.querySelector('select').value, 10);
				if( val === 0 ) {
					no++;
				} else if( val === 1 ) {
					yes++;
				} else if( val === 2 ) {
					maybe++;
				} else {
					count--;
				}
			} else {
				count--;
			}

			let yes_string = yes+'/'+count,
				no_string = no+'/'+count,
				maybe_string = maybe+'/'+count;

			if( max_yes === yes ) {
				if( yes === people_count ) {
					yes_string = '<strong>'+yes_string+'</strong>';
				} else {
					yes_string = '<strong>'+yes+'</strong>/'+count;
				}
			}
			if( max_no === no ) {
				no_string = '<strong>'+no+'</strong>/'+count;
			}


			tr.classList.remove('event-winner');
			tr.classList.remove('event-maybe-winner');
			
			if( yes === max_yes ) {
				tr.classList.add('event-winner');
			} else if( yes+maybe === max_yes_maybe ) {
				tr.classList.add('event-maybe-winner')
			}

			tr.querySelector('td.yes').innerHTML = yes_string;
			tr.querySelector('td.no').innerHTML = no_string;
			tr.querySelector('td.maybe').innerHTML = maybe_string;
		}

	}


	window.addEventListener('beforeunload', function(event){
		if( ! isModified ) return;
		event.preventDefault();
		event.returnValue = ''; // needed for some browsers
	});

})();