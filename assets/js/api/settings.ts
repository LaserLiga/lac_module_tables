import {customFetch, FormSaveResponse} from "../../../../../assets/js/includes/apiClient";


export async function deleteTable(id: number): Promise<FormSaveResponse> {
    return customFetch(`/settings/tables/${id}`, 'DELETE');
}