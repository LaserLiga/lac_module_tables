import {NewGameGroupInterface} from "../../../../assets/js/interfaces/groups";

window.addEventListener('load', () => {
	const gameTablesSelect = document.getElementById('table-select') as HTMLSelectElement | undefined;
	console.log(gameTablesSelect);

	document.addEventListener('clear-all', () => {
		if (gameTablesSelect) {
			gameTablesSelect.value = '';
		}
	});

    document.addEventListener('groups-module-loaded', (e: CustomEvent<NewGameGroupInterface>) => {
		console.log(e);
		import(
			/* webpackChunkName: "newGame_tables" */
			'./newGame/tables'
			)
			.then(module => {
				new module.default(e.detail, gameTablesSelect);
			});
	});
});