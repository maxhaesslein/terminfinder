
function initParticipationForm(){

	updateLineCounts();

	for( const input of document.querySelectorAll('input') ) {
		input.addEventListener('change', function(){
			handleSubmitButton();
		});
		input.addEventListener('input', function(){
			handleSubmitButton();
		});
	}

	for( const select of document.querySelectorAll('select') ) {
		if( select.id === 'sort-order' ) continue;
		select.addEventListener('change', function(){
			handleSubmitButton(); // needs to be called before 'updatePriorityState()'
			updateLineCounts();
			updatePriorityState();
		});
	}

	handleSorting();
	handlePersonHiding();
	handlePrioritySwitching();

	handleSubmitButton();
	updatePriorityState();

}
document.addEventListener( 'DOMContentLoaded', initParticipationForm, false );


function handleSorting(){
	const table = document.getElementById('schedule-list');
	if( ! table ) return;

	const sortWrapper = document.getElementById('sort-wrapper');
	if( ! sortWrapper ) return;

	const sortSelect = document.getElementById('sort-order');
	if( ! sortSelect ) return;

	sortWrapper.hidden = false;

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
			a = parseInt(rowA.dataset.sort, 10);
			b = parseInt(rowB.dataset.sort, 10);
		} else if( val === 'vote-count' ) {
			b = getCount(rowA);
			a = getCount(rowB);
		} else {
			return 0;
		}

		return a > b ? 1 : a < b ? -1 : 0;
	});

	rows.forEach(row => tbody.appendChild(row));


	// fix last column
	const toggle = table.querySelector('td.person-toggle');
	rows[0].appendChild(toggle);

}


function updateLineCounts() {
	const table = document.getElementById('schedule-list');

	if( ! table ) return;

	let priority = 1;
	const prioritySelect = document.getElementById('priority-select');
	if( prioritySelect ) {
		priority = parseInt(prioritySelect.value, 10);
		if( priority < 1 ) priority = 1;
	}

	const trs = table.querySelectorAll('tr.event-line');

	let people_count = parseInt(table.dataset.peopleCount, 10);

	let max_yes = 0,
		max_no = 0,
		max_yes_maybe = 0,
		best_matches = [];

	// determine points/winners for each line
	for( const tr of trs ) {

		let yes = parseInt(tr.dataset.yes_value, 10),
			no = parseInt(tr.dataset.no_value, 10),
			maybe = parseInt(tr.dataset.maybe_value, 10),
			id = tr.dataset.id;

		if( tr.querySelector('select') ) {
			const val = parseInt(tr.querySelector('select').value, 10);
			if( val === 0 ) {
				no += priority
			} else if( val === 1 ) {
				yes += priority;
			} else if( val === 2 ) {
				maybe += priority;
			}
		}

		if( yes > max_yes ) max_yes = yes;
		if( no > max_no ) max_no = no;
		if( yes+maybe > max_yes_maybe ) max_yes_maybe = yes+maybe;
		
		best_matches.push({
			'points': weightedPoints(yes, maybe, no),
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

	// update view count for each line
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
		
		if( winners[3] ) {
			// only show winners, if we have at least 3 steps to show

			if( winners[1] && winners[1].includes(id) ) {
				tr.classList.add('event-winner-1');
			} else if( winners[2] && winners[2].includes(id) ) {
				tr.classList.add('event-winner-2');
			} else if( winners[3] && winners[3].includes(id) ) {
				tr.classList.add('event-winner-3');
			}

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


function getCount(tr) {
	let yes = parseInt(tr.dataset.yes_value, 10),
		no = parseInt(tr.dataset.no_value, 10),
		maybe = parseInt(tr.dataset.maybe_value, 10),
		id = tr.dataset.id;

	let priority = 1;
	const prioritySelect = document.getElementById('priority-select');
	if( prioritySelect ) {
		priority = parseInt(prioritySelect.value, 10);
		if( priority < 1 ) priority = 1;
	}

	if( tr.querySelector('select') ) {
		const val = parseInt(tr.querySelector('select').value, 10);
		if( val === 0 ) {
			no += priority;
		} else if( val === 1 ) {
			yes += priority;
		} else if( val === 2 ) {
			maybe += priority;
		}
	}

	return weightedPoints(yes, maybe, no);
}


function weightedPoints( yes, maybe, no ) {
	// NOTE: this function also exists in include/helper.php
	return yes*4+maybe*2-no;
}


function handlePrioritySwitching(){

	const prioritySelect = document.getElementById('priority-select');
	if( ! prioritySelect ) return;

	prioritySelect.addEventListener('change', recheckPriorityConditions);

	recheckPriorityConditions();

	updateLineCounts();

}


function recheckPriorityConditions(){

	const currentPriority = document.getElementById('priority-select').value;

	for( const span of document.querySelectorAll('.priority-select-description') ) {
		span.classList.remove('priority-select-description--visible');
	}

	const newPriorityDescription = document.getElementById('priority-select-description-'+currentPriority);
	if( ! newPriorityDescription ) return;

	newPriorityDescription.classList.add('priority-select-description--visible');

}


function updatePriorityState() {

	const prioritySelect = document.getElementById('priority-select');
	if( ! prioritySelect ) return;

	const priority = parseInt(prioritySelect.value, 10);

	let passCheck = false;
	let missing = 0;

	const dateSelects = document.querySelectorAll('select[name^="entry_"]');

	if( priority === 1 ) { // optional
		
		// NOTE: allow all combinations
		passCheck = true;

	} else if( priority === 2 ) { // prefer to attend

		// NOTE: we want to have 'yes' or 'maybe' for at least one third (rounded up) of the dates

		let yes_maybe_count = 0;

		for( const select of dateSelects ) {
			const selectValue = parseInt(select.value, 10);

			if( selectValue === 1 || selectValue === 2 ) {
				yes_maybe_count++;
			}
		}

		const targetCount = Math.ceil(dateSelects.length * 1/3);

		if( yes_maybe_count >= targetCount ) {
			passCheck = true;
		} else {
			missing = targetCount - yes_maybe_count;
		}

	} else if( priority === 3 ) { // really want to attend

		// NOTE: we want to have 'yes' for at least one third (rounded up) of the dates

		let yes_count = 0;

		for( const select of dateSelects ) {
			const selectValue = parseInt(select.value, 10);

			if( selectValue === 1 ) {
				yes_count++;
			}
		}

		const targetCount = Math.ceil(dateSelects.length * 1/3);

		if( yes_count >= targetCount ) {
			passCheck = true;
		} else {
			missing = targetCount - yes_count;
		}

	}

	if( ! passCheck ) {
		document.getElementById('priority-select-wrapper').classList.add('priority-select-wrapper--nopass');
		document.getElementById('submit-button').disabled = true; // force submit button to disabled
	} else {
		document.getElementById('priority-select-wrapper').classList.remove('priority-select-wrapper--nopass');
	}

	if( missing > 0 ) {
		document.getElementById('priority-select-missing-count').innerText = missing;
		document.getElementById('priority-select-missing').classList.add('priority-select-missing--visible');
	} else {
		document.getElementById('priority-select-missing-count').innerText = '';
		document.getElementById('priority-select-missing').classList.remove('priority-select-missing--visible');
	}

}


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

	submitButton.disabled = ! readyToSend;

}
