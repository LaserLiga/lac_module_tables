import {customFetch, ErrorResponse} from "../../../../../assets/js/includes/apiClient";


export async function deleteTable(id: number): Promise<'' | ErrorResponse> {
    return customFetch(`/settings/tables/${id}`, 'DELETE');
}