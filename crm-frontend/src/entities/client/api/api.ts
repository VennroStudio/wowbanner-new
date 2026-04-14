import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/constants';
import type { Client, ClientEnumOption, GetClientsParams, PaginatedResponse } from '../model/types';

export type CreateClientPhone = {
  type: number;
  phone: string;
};

export type CreateClientCompany = {
  name: string;
};

export type CreateClientBody = {
  lastName: string;
  firstName: string;
  middleName?: string;
  email?: string;
  docs: number;
  type: number;
  info?: string;
  phones?: CreateClientPhone[];
  companies?: CreateClientCompany[];
};

export type UpdateClientBody = Partial<CreateClientBody> & {
  phones?: ({ id?: number } & CreateClientPhone)[];
  companies?: ({ id?: number } & CreateClientCompany)[];
};

export const clientApi = {
  getClients: async (params?: GetClientsParams) => {
    const { data } = await apiClient.get<PaginatedResponse<Client>>(API_ENDPOINTS.CLIENTS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
      },
    });
    return data;
  },

  getClient: async (id: number | string) => {
    const { data } = await apiClient.get<{ data: Client }>(API_ENDPOINTS.CLIENTS.BY_ID(id));
    return data;
  },

  createClient: async (body: CreateClientBody) => {
    const { data } = await apiClient.post<{ data: number }>(API_ENDPOINTS.CLIENTS.CREATE, body);
    return data;
  },

  updateClient: async (id: number | string, body: UpdateClientBody) => {
    const { data } = await apiClient.patch<{ data: number }>(API_ENDPOINTS.CLIENTS.UPDATE(id), body);
    return data;
  },

  deleteClient: async (id: number | string) => {
    const { data } = await apiClient.delete<{ data: number }>(API_ENDPOINTS.CLIENTS.DELETE(id));
    return data;
  },

  getClientTypes: async () => {
    const { data } = await apiClient.get<{ data: ClientEnumOption[] }>(API_ENDPOINTS.CLIENTS.TYPES);
    return data.data;
  },

  getClientDocsTypes: async () => {
    const { data } = await apiClient.get<{ data: ClientEnumOption[] }>(API_ENDPOINTS.CLIENTS.DOCS_TYPES);
    return data.data;
  },

  getClientPhoneTypes: async () => {
    const { data } = await apiClient.get<{ data: ClientEnumOption[] }>(API_ENDPOINTS.CLIENTS.PHONE_TYPES);
    return data.data;
  },
};
