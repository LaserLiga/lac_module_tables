import {fetchGet, fetchPost} from '../../../../../assets/js/includes/apiClient';
import {TableData} from '../newGame/interfaces';

export type TablesResponse = { tables: TableData[] }

export async function getTables(): Promise<TablesResponse> {
    return fetchGet('/tables', null, {Accept: 'application/json'});
}

export async function getTable(id: number): Promise<TableData> {
    return fetchGet(`/tables/${id}`, null, {Accept: 'application/json'});
}

export async function cleanTable(id: number): Promise<TableData> {
    return fetchPost(`/tables/${id}/clean`, null, {Accept: 'application/json'});
}