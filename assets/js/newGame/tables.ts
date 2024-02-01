import {TableData} from "./interfaces";
import {GameGroupDataBase} from "../../../../../assets/js/interfaces/gameInterfaces";
import {cleanTable, getTable, getTables} from "../api/tables";
import {lang} from "../../../../../assets/js/includes/frameworkFunctions";
import {NewGameGroupInterface} from "../../../../../assets/js/interfaces/groups";

export default class NewGameTables {

    groups: NewGameGroupInterface;
    gameTablesSelect: HTMLSelectElement;

    groupMap: Map<number, number> = new Map;

    constructor(groups: NewGameGroupInterface, gameTablesSelect: HTMLSelectElement) {
        this.groups = groups;
        this.gameTablesSelect = gameTablesSelect;

        this.gameTablesSelect.addEventListener('change', () => {
            this.updateTables();
            this.selectTable(this.gameTablesSelect.value);
        });

        (document.querySelectorAll('.game-table') as NodeListOf<HTMLDivElement>).forEach(table => {
            this.initTable(table);
        });
        document.getElementById('tables').addEventListener('show.bs.offcanvas', () => {
            this.updateTables();
        });

        document.addEventListener('game-group-loaded', (e: CustomEvent<GameGroupDataBase>) => {
            console.log(e, this.groupMap.entries());
            const id = this.groupMap.get(e.detail.id);
            if (id) {
                this._selectTable(id);
            }
        });

        document.addEventListener('game-group-selected', (e: CustomEvent<GameGroupDataBase>) => {
            console.log(e, this.groupMap.entries());
            const id = this.groupMap.get(e.detail.id);
            if (id) {
                this._selectTable(id);
            }
        });

        document.addEventListener('game-group-import', (e: CustomEvent<GameGroupDataBase>) => {
            console.log(e, this.groupMap.entries());
            const id = this.groupMap.get(e.detail.id);
            if (id) {
                this._selectTable(id);
            }
        });
    }

    initTable(table: HTMLDivElement): void {
        const id = parseInt(table.dataset.id);

        if (table.dataset.group) {
            this.groupMap.set(parseInt(table.dataset.group), id);
        }

        const cleanBtn = table.querySelector('.clean') as HTMLButtonElement;
        const loadBtn = table.querySelector('.load') as HTMLButtonElement;

        table.addEventListener('click', (e: MouseEvent) => {
            // Prevent trigger if clicked on the cleanBtn
            const target = e.target as HTMLElement;
            if (target === cleanBtn || target.parentElement === cleanBtn || target === loadBtn || target.parentElement === loadBtn) {
                return;
            }
            this.selectTable(id);
        });

        cleanBtn.addEventListener('click', () => {
            document.dispatchEvent(new CustomEvent('loading.start'));
            cleanTable(id)
                .then(() => {
                    this.updateTable(id);
                    document.dispatchEvent(new CustomEvent('loading.stop'));
                })
                .catch(() => {
                    document.dispatchEvent(new CustomEvent('loading.error'));
                })
        });

        loadBtn.addEventListener('click', async () => {
            await this.loadTable(id);
        });
    }

    async loadTable(id: number | string) {
        const table = document.querySelector(`.game-table[data-id="${id}"]`) as HTMLDivElement | null;
        if (!table) {
            return;
        }

        if (table.dataset.group) {
            const groupId = parseInt(table.dataset.group);
            let groupDom = this.groups.gameGroupsWrapper.querySelector(`.game-group[data-id="${groupId}"]`) as HTMLDivElement;
            if (!groupDom) {
                // Load group if it doesn't exist (for example if it's disabled)
                document.dispatchEvent(new CustomEvent('loading.small.start'));
                await this.groups.loadGroup(groupId);
                groupDom = this.groups.gameGroupsWrapper.querySelector(`.game-group[data-id="${groupId}"]`) as HTMLDivElement;
                document.dispatchEvent(new CustomEvent('loading.small.stop'));
            }
            // Dispatch a click event on the loadPlayers btn
            groupDom.querySelector('.loadPlayers').dispatchEvent(new Event('click', {bubbles: true}));
        } else {
            this.groups.gameGroupsSelect.value = "";
        }
        this._selectTable(id);
        this.gameTablesSelect.dispatchEvent(new Event('update', {bubbles: true}));
    }

    selectTable(id: number | string) {
        this._selectTable(id);

        this.gameTablesSelect.dispatchEvent(new Event('update', {bubbles: true}));
    }

    updateTableData(table: TableData) {
        const tableDom = document.querySelector(`.game-table[data-id="${table.id}"]`) as HTMLDivElement | null;
        if (!tableDom) {
            return;
        }
        const cleanBtn = tableDom.querySelector('.clean') as HTMLButtonElement;
        const loadBtn = tableDom.querySelector('.load') as HTMLButtonElement;

        if (table.group) {
            this.groupMap.set(table.group.id, table.id);
            tableDom.dataset.group = table.group.id.toString();
            if (tableDom.classList.contains('bg-purple-400')) {
                tableDom.classList.remove('bg-purple-400', 'text-bg-purple-400');
                tableDom.classList.add('bg-purple-600', 'text-bg-purple-600');
            }
            cleanBtn.classList.remove('d-none');
            loadBtn.classList.remove('d-none');
            const option = this.gameTablesSelect.querySelector(`[value="${table.id}"]`) as HTMLOptionElement | null;
            if (option) {
                lang('Obsazený')
                    .then(response => {
                        option.innerText = table.name + " " + response;
                    });
            }
        } else {
            tableDom.dataset.group = "";
            if (tableDom.classList.contains('bg-purple-600')) {
                tableDom.classList.remove('bg-purple-600', 'text-bg-purple-600');
                tableDom.classList.add('bg-purple-400', 'text-bg-purple-400');
            }
            cleanBtn.classList.add('d-none');
            loadBtn.classList.add('d-none');
            const option = this.gameTablesSelect.querySelector(`[value="${table.id}"]`) as HTMLOptionElement | null;
            if (option) {
                option.innerText = table.name;
            }
        }
    }

    updateTable(id: number): void {
        getTable(id).then(this.updateTableData);
    }

    updateTables(): void {
        getTables()
            .then(response => {
                this.groupMap.clear();
                response.tables.forEach(table => this.updateTableData(table));
            })
    }

    private _selectTable(id: number | string) {
        console.log('Selecting table', id);
        const activeTable = document.querySelector('.game-table.active') as HTMLDivElement | null;
        if (activeTable) {
            activeTable.classList.remove('active', 'bg-success', 'text-bg-success');
            if (activeTable.dataset.group) {
                activeTable.classList.add('bg-purple-600', 'text-bg-purple-600');
            } else {
                activeTable.classList.add('bg-purple-400', 'text-bg-purple-400');
            }
        }
        const table = document.querySelector(`.game-table[data-id="${id}"]`) as HTMLDivElement | null;
        if (!table) {
            return;
        }
        console.log(table, table.dataset.group ?? "");
        table.classList.remove('bg-purple-400', 'bg-purple-600', 'text-bg-purple-400', 'text-bg-purple-600');
        table.classList.add('active', 'bg-success', 'text-bg-success');

        this.gameTablesSelect.value = id.toString();
        if (table.dataset.group) {
            const groupId = parseInt(table.dataset.group);
            const groupSelect = document.getElementById('group-select') as HTMLSelectElement;
            if (groupSelect) {
                groupSelect.value = groupId.toString();
                if (groupSelect.value !== groupId.toString()) {
                    groupSelect.value = '';
                }
            }
        }
    }

}