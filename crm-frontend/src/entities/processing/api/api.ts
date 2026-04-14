import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/constants';
import type {
  GetProcessingsParams,
  PaginatedProcessingsResponse,
  Processing,
  ProcessingTypeRef,
} from '../model/types';

export type CreateProcessingBody = {
  name: string;
  description?: string;
  type: number;
  costPrice: string;
  price: string;
};

export type UpdateProcessingBody = CreateProcessingBody;

export const processingApi = {
  getProcessings: async (params?: GetProcessingsParams) => {
    const { data } = await apiClient.get<PaginatedProcessingsResponse>(API_ENDPOINTS.PROCESSINGS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
      },
    });
    return data;
  },

  getProcessing: async (id: number | string) => {
    const { data } = await apiClient.get<{ data: Processing }>(API_ENDPOINTS.PROCESSINGS.BY_ID(id));
    return data;
  },

  getProcessingTypes: async (): Promise<ProcessingTypeRef[]> => {
    const { data } = await apiClient.get<{ data: ProcessingTypeRef[] }>(API_ENDPOINTS.PROCESSINGS.TYPES);
    return data.data;
  },

  createProcessing: async (body: CreateProcessingBody) => {
    const { data } = await apiClient.post(API_ENDPOINTS.PROCESSINGS.CREATE, body);
    return data;
  },

  updateProcessing: async (id: number | string, body: UpdateProcessingBody) => {
    const { data } = await apiClient.patch(API_ENDPOINTS.PROCESSINGS.UPDATE(id), body);
    return data;
  },

  deleteProcessing: async (id: number | string) => {
    const { data } = await apiClient.delete(API_ENDPOINTS.PROCESSINGS.DELETE(id));
    return data;
  },

  uploadImages: async (processingId: number | string, files: File[], imageAlts: string[]) => {
    const formData = new FormData();
    files.forEach((file) => {
      formData.append('images[]', file);
    });
    imageAlts.forEach((alt) => {
      formData.append('imageAlts[]', alt);
    });
    const { data } = await apiClient.post(API_ENDPOINTS.PROCESSINGS.IMAGES(processingId), formData);
    return data;
  },

  updateImageAlt: async (imageId: number | string, alt: string) => {
    const { data } = await apiClient.patch(API_ENDPOINTS.PROCESSINGS.IMAGE_UPDATE(imageId), { alt });
    return data;
  },

  deleteImage: async (imageId: number | string) => {
    const { data } = await apiClient.delete(API_ENDPOINTS.PROCESSINGS.IMAGE_DELETE(imageId));
    return data;
  },
};
