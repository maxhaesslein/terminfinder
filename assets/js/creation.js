
function initCreationForm(){
	handleDragDrop();

	const addEventButton = document.getElementById('add-event');
	if( addEventButton ) {
		addEventButton.addEventListener('click', addEvent);
	}
	
}
document.addEventListener( 'DOMContentLoaded', initCreationForm, false );


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
