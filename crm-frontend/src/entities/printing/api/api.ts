import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/constants';
import type { GetPrintingsParams, PaginatedPrintingsResponse, Printing } from '../model/types';

export type CreatePrintingBody = {
  name: string;
};

export type UpdatePrintingBody = {
  name: string;
};

export const printingApi = {
  getPrintings: async (params?: GetPrintingsParams) => {
    const { data } = await apiClient.get<PaginatedPrintingsResponse>(API_ENDPOINTS.PRINTINGS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
      },
    });
    return data;
  },

  getPrinting: async (id: number | string) => {
    const { data } = await apiClient.get<{ data: Printing }>(
      API_ENDPOINTS.PRINTINGS.BY_ID(id),
    );
    return data;
  },

  createPrinting: async (body: CreatePrintingBody) => {
    const { data } = await apiClient.post(API_ENDPOINTS.PRINTINGS.CREATE, body);
    return data;
  },

  updatePrinting: async (id: number | string, body: UpdatePrintingBody) => {
    const { data } = await apiClient.patch(API_ENDPOINTS.PRINTINGS.UPDATE(id), body);
    return data;
  },

  deletePrinting: async (id: number | string) => {
    const { data } = await apiClient.delete(API_ENDPOINTS.PRINTINGS.DELETE(id));
    return data;
  },
};
