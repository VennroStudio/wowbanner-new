import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/constants';
import type { GetMaterialsParams, Material, PaginatedResponse } from '../model/types';

export type CreateMaterialBody = {
  name: string;
  description?: string;
};

export type UpdateMaterialBody = {
  name: string;
  description?: string;
};

export const materialApi = {
  getMaterials: async (params?: GetMaterialsParams) => {
    const { data } = await apiClient.get<PaginatedResponse<Material>>(API_ENDPOINTS.MATERIALS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
      },
    });
    return data;
  },

  getMaterial: async (id: number | string) => {
    const { data } = await apiClient.get<{ data: Material }>(API_ENDPOINTS.MATERIALS.BY_ID(id));
    return data;
  },

  createMaterial: async (body: CreateMaterialBody) => {
    const { data } = await apiClient.post(API_ENDPOINTS.MATERIALS.CREATE, body);
    return data;
  },

  updateMaterial: async (id: number | string, body: UpdateMaterialBody) => {
    const { data } = await apiClient.patch(API_ENDPOINTS.MATERIALS.UPDATE(id), body);
    return data;
  },

  deleteMaterial: async (id: number | string) => {
    const { data } = await apiClient.delete(API_ENDPOINTS.MATERIALS.DELETE(id));
    return data;
  },

  uploadImages: async (materialId: number | string, files: File[], imageAlts: string[]) => {
    const formData = new FormData();
    files.forEach((file) => {
      formData.append('images[]', file);
    });
    imageAlts.forEach((alt) => {
      formData.append('imageAlts[]', alt);
    });
    const { data } = await apiClient.post(API_ENDPOINTS.MATERIALS.IMAGES(materialId), formData);
    return data;
  },

  updateImageAlt: async (imageId: number | string, alt: string) => {
    const { data } = await apiClient.patch(API_ENDPOINTS.MATERIALS.IMAGE_UPDATE(imageId), { alt });
    return data;
  },

  deleteImage: async (imageId: number | string) => {
    const { data } = await apiClient.delete(API_ENDPOINTS.MATERIALS.IMAGE_DELETE(imageId));
    return data;
  },
};
