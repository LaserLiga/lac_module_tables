import {fetchGet, FormSaveResponse} from "../../../../../assets/js/includes/apiClient";
import {TableData} from "../newGame/interfaces";

export type TablesResponse = { tables: TableData[] }

export async function getTables(): Promise<TablesResponse> {
    return fetchGet('/tables');
}

export async function getTable(id: number): Promise<TableData> {
    return fetchGet(`/tables/${id}`);
}

export async function cleanTable(id: number): Promise<FormSaveResponse> {
    return fetchGet(`/tables/${id}/clean`);
}