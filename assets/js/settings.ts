import {startLoading, stopLoading} from '../../../../assets/js/loaders';
import {deleteTable} from './api/settings';
import DraggableGrid from '../../../../assets/js/components/draggableGrid';

window.addEventListener('load', () => {
	const tables = document.querySelectorAll('.table-item') as NodeListOf<HTMLDivElement>;
	const tablesSection = document.getElementById('tables') as HTMLDivElement;
	const columnsInput = document.querySelector<HTMLInputElement>('.columns-input');
	const rowsInput = document.querySelector<HTMLInputElement>('.rows-input');

	const draggable = new DraggableGrid(tablesSection, columnsInput.valueAsNumber, rowsInput.valueAsNumber);

	columnsInput.addEventListener('input', () => {
		draggable.updateColumns(columnsInput.valueAsNumber);
	});
	rowsInput.addEventListener('input', () => {
		draggable.updateRows(rowsInput.valueAsNumber);
	});

	tables.forEach(table => {
		const id = parseInt(table.dataset.id);
		const deleteBtn = table.querySelector('.delete') as HTMLButtonElement;

		deleteBtn.addEventListener('click', () => {
			startLoading();
            deleteTable(id)
				.then(() => {
					stopLoading(true);
					const empty = document.createElement('div');
					empty.classList.add('draggable-empty');
					empty.dataset.row = table.dataset.row;
					empty.dataset.column = table.dataset.column;
					table.replaceWith(empty);
					table.remove();
				})
				.catch(error => {
					console.error(error);
					stopLoading(false);
				})
		});
	});
});