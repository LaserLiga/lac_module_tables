import {GameGroupData} from "../../../../../assets/js/interfaces/gameInterfaces";

interface TableData {
	id: number,
	name: string,
	group?: GameGroupData | null,
	grid?: {
		row: number,
		col: number,
		width: number,
		height: number,
	}
}