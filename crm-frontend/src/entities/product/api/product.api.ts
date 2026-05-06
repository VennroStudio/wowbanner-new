import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import type { ApiDataResponse, ApiMutationResponse } from '@/shared/api/types';
import type {
  GetProductsParams,
  PaginatedProductsResponse,
  Product,
  ProductMaterialPayload,
  ProductPrintPayload,
} from '../model/types';

export type CreateProductBody = {
  name: string;
  materials?: ProductMaterialPayload[];
  prints?: ProductPrintPayload[];
};

export type UpdateProductBody = CreateProductBody;

export const productApi = {
  getProducts: async (params?: GetProductsParams) => {
    const { data } = await apiClient.get<PaginatedProductsResponse>(API_ENDPOINTS.PRODUCTS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
      },
    });
    return data;
  },

  getProduct: async (id: number | string) => {
    const { data } = await apiClient.get<ApiDataResponse<Product>>(API_ENDPOINTS.PRODUCTS.BY_ID(id));
    return data;
  },

  createProduct: async (body: CreateProductBody) => {
    const { data } = await apiClient.post<ApiMutationResponse>(API_ENDPOINTS.PRODUCTS.CREATE, body);
    return data;
  },

  updateProduct: async (id: number | string, body: UpdateProductBody) => {
    const { data } = await apiClient.patch<ApiMutationResponse>(API_ENDPOINTS.PRODUCTS.UPDATE(id), body);
    return data;
  },

  deleteProduct: async (id: number | string) => {
    const { data } = await apiClient.delete<ApiMutationResponse>(API_ENDPOINTS.PRODUCTS.DELETE(id));
    return data;
  },
};
