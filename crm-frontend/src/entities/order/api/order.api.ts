import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import { toFormData } from '@/shared/api/toFormData';
import type { ApiDataResponse, ApiMutationResponse } from '@/shared/api/types';
import type {
  CreateOrderBody,
  GetOrdersParams,
  Order,
  OrderEnumRef,
  PaginatedOrdersResponse,
  UpdateOrderBody,
} from '../model/types';

export const orderApi = {
  getOrders: async (params?: GetOrdersParams) => {
    const { data } = await apiClient.get<PaginatedOrdersResponse>(API_ENDPOINTS.ORDERS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
        dateFrom: params?.dateFrom,
        dateTo: params?.dateTo,
        printIds: params?.printIds,
        materialId: params?.materialId,
        optionId: params?.optionId,
        docs: params?.docs,
        managerId: params?.managerId,
        designerId: params?.designerId,
        statusTypes: params?.statusTypes,
        storageType: params?.storageType,
        serviceType: params?.serviceType,
        archived: params?.archived,
        deleted: params?.deleted,
      },
    });
    return data;
  },

  getOrder: async (id: number | string) => {
    const { data } = await apiClient.get<ApiDataResponse<Order>>(API_ENDPOINTS.ORDERS.BY_ID(id));
    return data;
  },

  createOrder: async (body: CreateOrderBody) => {
    const formData = toFormData(body);
    const { data } = await apiClient.post<ApiMutationResponse>(API_ENDPOINTS.ORDERS.CREATE, formData);
    return data;
  },

  updateOrder: async (id: number | string, body: UpdateOrderBody) => {
    const hasUploadFiles = body.files?.some((file) => file instanceof File) ?? false;

    if (hasUploadFiles) {
      const formData = toFormData(body);
      const { data } = await apiClient.post<ApiMutationResponse>(API_ENDPOINTS.ORDERS.UPDATE(id), formData);
      return data;
    }

    const { data } = await apiClient.patch<ApiMutationResponse>(API_ENDPOINTS.ORDERS.UPDATE(id), {
      ...body,
      files: undefined,
      fileOriginalNames: undefined,
    });
    return data;
  },

  deleteOrder: async (id: number | string) => {
    const { data } = await apiClient.delete<ApiMutationResponse>(API_ENDPOINTS.ORDERS.DELETE(id));
    return data;
  },

  downloadOrderFile: async (id: number | string) => {
    const { data } = await apiClient.get<Blob>(API_ENDPOINTS.ORDERS.FILE_DOWNLOAD(id), {
      responseType: 'blob',
    });
    return data;
  },

  deleteOrderFile: async (id: number | string) => {
    const { data } = await apiClient.delete<ApiMutationResponse>(API_ENDPOINTS.ORDERS.FILE_DELETE(id));
    return data;
  },

  getStatusTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(API_ENDPOINTS.ORDERS.STATUS_TYPES);
    return data.data;
  },

  getStorageTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(API_ENDPOINTS.ORDERS.STORAGE_TYPES);
    return data.data;
  },

  getDeliveryTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(API_ENDPOINTS.ORDERS.DELIVERY_TYPES);
    return data.data;
  },

  getPaymentOperationTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(
      API_ENDPOINTS.ORDERS.PAYMENT_OPERATION_TYPES,
    );
    return data.data;
  },

  getPaymentTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(API_ENDPOINTS.ORDERS.PAYMENT_TYPES);
    return data.data;
  },

  getSectionTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(API_ENDPOINTS.ORDERS.SECTION_TYPES);
    return data.data;
  },

  getServiceTypes: async (): Promise<OrderEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<OrderEnumRef[]>>(API_ENDPOINTS.ORDERS.SERVICE_TYPES);
    return data.data;
  },
};
