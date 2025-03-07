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
			if( select.id === 'sort-order' ) continue;
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
		handleSorting();
		handlePersonHiding();

	}	
	document.addEventListener( 'DOMContentLoaded', init, false );


	function handleSorting(){
		const table = document.getElementById('schedule-list');
		if( ! table ) return;

		const sortWrapper = document.getElementById('sort-wrapper');
		if( ! sortWrapper ) return;

		const sortSelect = document.getElementById('sort-order');
		if( ! sortSelect ) return;

		sortWrapper.hidden = false;

		var i = 0;
		for( const tr of table.querySelector('tbody').querySelectorAll('tr') ) {
			tr.dataset.originalOrder = i;
			i++;
		}

		sortSelect.addEventListener('change', updateSorting);

		updateSorting();

	}

	function updateSorting() {

		const sortSelect = document.getElementById('sort-order');
		if( ! sortSelect ) return;

		const table = document.getElementById('schedule-list');
		if( ! table ) return;

		const tbody = table.querySelector('tbody');

		const val = sortSelect.value;

		let rows = Array.from(tbody.getElementsByTagName("tr"));

		rows.sort(function(rowA, rowB){
			let a,b;

			if( val === 'chronological' ) {
				a = parseInt(rowA.dataset.originalOrder, 10);
				b = parseInt(rowB.dataset.originalOrder, 10);
			} else if( val === 'vote-count' ) {
				b = getCount(rowA);
				a = getCount(rowB);
			} else {
				return 0;
			}

			return a > b ? 1 : a < b ? -1 : 0;
		});

		rows.forEach(row => tbody.appendChild(row));

	}


	function getCount(tr) {
		let yes = parseInt(tr.dataset.yes, 10),
			no = parseInt(tr.dataset.no, 10),
			maybe = parseInt(tr.dataset.maybe, 10),
			id = tr.dataset.id;

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

		return yes*3+maybe*2-no;
	}


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
			max_yes_maybe = 0,
			best_matches = [];

		for( const tr of trs ) {

			let yes = parseInt(tr.dataset.yes, 10),
				no = parseInt(tr.dataset.no, 10),
				maybe = parseInt(tr.dataset.maybe, 10),
				id = tr.dataset.id;

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
			
			best_matches.push({
				'points': yes*3 + maybe*2 - no,
				'id': id
			});

		}

		let winners = {};

		if( best_matches.length ) {
			best_matches.sort(function(a, b){ return b.points-a.points; });

			let c_points = best_matches[0].points,
				place = 1;
			for( var best_match of best_matches ) {
				if( best_match.points < c_points ) {
					place++;
					c_points = best_match.points;
				}
				if( place > 3 ) break;

				if( ! winners[place] ) winners[place] = [];

				winners[place].push(best_match.id);
			}

		}


		for( const tr of trs ) {
			let yes = parseInt(tr.dataset.yes, 10),
				no = parseInt(tr.dataset.no, 10),
				maybe = parseInt(tr.dataset.maybe, 10),
				id = tr.dataset.id;

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

			let yes_string = yes,
				no_string = no,
				maybe_string = maybe;

			if( max_yes === yes ) {
				yes_string = '<strong>'+yes_string+'</strong>';
			}
			if( max_no === no ) {
				no_string = '<strong>'+no+'</strong>';
			}

			tr.classList.remove('event-winner-1', 'event-winner-2', 'event-winner-3');
			
			if( winners[1] && winners[1].includes(id) ) {
				tr.classList.add('event-winner-1');
			} else if( winners[2] && winners[2].includes(id) ) {
				tr.classList.add('event-winner-2');
			} else if( winners[3] && winners[3].includes(id) ) {
				tr.classList.add('event-winner-3');
			}

			tr.querySelector('td.yes').innerHTML = yes_string;
			tr.querySelector('td.no').innerHTML = no_string;
			tr.querySelector('td.maybe').innerHTML = maybe_string;
		}

		updateSorting();

	}


	function handlePersonHiding(){

		const table = document.getElementById('schedule-list');
		if( ! table ) return;

		const persons = table.querySelectorAll('.person');
		if( ! persons ) return;

		const personToggles = table.querySelectorAll('.person-toggle');
		if( ! personToggles ) return;

		for( const person of persons ) {
			person.classList.add('hidden');
		}

		for( const toggle of personToggles ) {
			toggle.classList.add('visible');
		}

		table.querySelector('td.person-toggle').addEventListener('click', function(){
			for( const person of persons ) {
				person.classList.toggle('hidden');
			}
		});

	}


	window.addEventListener('beforeunload', function(event){
		if( ! isModified ) return;
		event.preventDefault();
		event.returnValue = ''; // needed for some browsers
	});

})();