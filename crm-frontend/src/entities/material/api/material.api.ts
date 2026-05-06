import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import type { ApiDataResponse, ApiMutationResponse } from '@/shared/api/types';
import type {
  GetMaterialsParams,
  Material,
  MaterialCreateUpdateOption,
  MaterialEnumRef,
  MaterialOptionSelectOption,
  MaterialSelectOption,
  PaginatedResponse,
} from '../model/types';

export type CreateMaterialBody = {
  name: string;
  description?: string;
  options?: MaterialCreateUpdateOption[];
};

export type UpdateMaterialBody = {
  name: string;
  description?: string;
  options?: MaterialCreateUpdateOption[];
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
    const { data } = await apiClient.get<ApiDataResponse<Material>>(API_ENDPOINTS.MATERIALS.BY_ID(id));
    return data;
  },

  getMaterialSelectOptions: async (): Promise<MaterialSelectOption[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialSelectOption[]>>(
      API_ENDPOINTS.MATERIALS.SELECT,
    );
    return data.data;
  },

  getMaterialOptionSelectOptions: async (
    materialId: number | string,
  ): Promise<MaterialOptionSelectOption[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialOptionSelectOption[]>>(
      API_ENDPOINTS.MATERIALS.OPTION_SELECT(materialId),
    );
    return data.data;
  },

  createMaterial: async (body: CreateMaterialBody) => {
    const { data } = await apiClient.post<ApiMutationResponse>(API_ENDPOINTS.MATERIALS.CREATE, body);
    return data;
  },

  updateMaterial: async (id: number | string, body: UpdateMaterialBody) => {
    const { data } = await apiClient.patch<ApiMutationResponse>(API_ENDPOINTS.MATERIALS.UPDATE(id), body);
    return data;
  },

  deleteMaterial: async (id: number | string) => {
    const { data } = await apiClient.delete<ApiMutationResponse>(API_ENDPOINTS.MATERIALS.DELETE(id));
    return data;
  },

  getOptionPricingTypes: async (): Promise<MaterialEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialEnumRef[]>>(
      API_ENDPOINTS.MATERIALS.OPTION_PRICING_TYPES,
    );
    return data.data;
  },

  getAreaRangeTypes: async (): Promise<MaterialEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialEnumRef[]>>(
      API_ENDPOINTS.MATERIALS.AREA_RANGE_TYPES,
    );
    return data.data;
  },

  getDpiTypes: async (): Promise<MaterialEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialEnumRef[]>>(
      API_ENDPOINTS.MATERIALS.DPI_TYPES,
    );
    return data.data;
  },

  getVariantTypes: async (): Promise<MaterialEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialEnumRef[]>>(
      API_ENDPOINTS.MATERIALS.VARIANT_TYPES,
    );
    return data.data;
  },

  getPricingCutTypes: async (): Promise<MaterialEnumRef[]> => {
    const { data } = await apiClient.get<ApiDataResponse<MaterialEnumRef[]>>(
      API_ENDPOINTS.MATERIALS.PRICING_CUT_TYPES,
    );
    return data.data;
  },

  uploadImages: async (materialId: number | string, files: File[], imageAlts: string[]) => {
    const formData = new FormData();
    files.forEach((file) => {
      formData.append('images[]', file);
    });
    imageAlts.forEach((alt) => {
      formData.append('imageAlts[]', alt);
    });
    const { data } = await apiClient.post<ApiMutationResponse>(API_ENDPOINTS.MATERIALS.IMAGES(materialId), formData);
    return data;
  },

  updateImageAlt: async (imageId: number | string, alt: string) => {
    const { data } = await apiClient.patch<ApiMutationResponse>(API_ENDPOINTS.MATERIALS.IMAGE_UPDATE(imageId), { alt });
    return data;
  },

  deleteImage: async (imageId: number | string) => {
    const { data } = await apiClient.delete<ApiMutationResponse>(API_ENDPOINTS.MATERIALS.IMAGE_DELETE(imageId));
    return data;
  },
};
