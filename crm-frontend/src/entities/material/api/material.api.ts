import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import type { ApiDataResponse, ApiMutationResponse } from '@/shared/api/types';
import type {
  GetMaterialsParams,
  Material,
  MaterialCreateUpdateOption,
  MaterialEnumRef,
  MaterialImage,
  MaterialOption,
  MaterialOptionSelectOption,
  MaterialSelectOption,
  PaginatedResponse,
} from '../model/types';

type ApiMaterialOptionProcessing = {
  id: number;
  processing_id: number;
  processing_name?: string;
};

type ApiMaterialPricingByAreaRow = {
  id: number;
  dpi_type: MaterialEnumRef;
  area_range_type: MaterialEnumRef;
  price: string;
  cost: string;
  print_hours: string;
};

type ApiMaterialPricingByPieceRow = {
  id: number;
  variant_type: MaterialEnumRef;
  price: string;
  cost: string;
  print_hours: string;
};

type ApiMaterialPricingByCutRow = {
  id: number;
  type: MaterialEnumRef;
  price: string;
};

type ApiMaterialOption = {
  id: number;
  name: string;
  pricing_type: MaterialEnumRef;
  is_cut: boolean;
  pricing_by_area?: ApiMaterialPricingByAreaRow[];
  pricing_by_piece?: ApiMaterialPricingByPieceRow[];
  pricing_by_cut?: ApiMaterialPricingByCutRow[];
  processings?: ApiMaterialOptionProcessing[];
};

type ApiMaterial = {
  id: number;
  name: string;
  description: string;
  images?: MaterialImage[];
  options?: ApiMaterialOption[];
};

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

const mapMaterialOption = (option: ApiMaterialOption): MaterialOption => ({
  id: option.id,
  name: option.name,
  pricingType: option.pricing_type,
  isCut: option.is_cut,
  pricingByArea: (option.pricing_by_area ?? []).map((row) => ({
    id: row.id,
    dpiType: row.dpi_type,
    areaRangeType: row.area_range_type,
    price: row.price,
    cost: row.cost,
    printHours: row.print_hours,
  })),
  pricingByPiece: (option.pricing_by_piece ?? []).map((row) => ({
    id: row.id,
    variantType: row.variant_type,
    price: row.price,
    cost: row.cost,
    printHours: row.print_hours,
  })),
  pricingByCut: (option.pricing_by_cut ?? []).map((row) => ({
    id: row.id,
    type: row.type,
    price: row.price,
  })),
  processings: (option.processings ?? []).map((processing) => ({
    id: processing.id,
    processingId: processing.processing_id,
    processingName: processing.processing_name,
  })),
});

const mapMaterial = (material: ApiMaterial): Material => ({
  id: material.id,
  name: material.name,
  description: material.description,
  images: material.images ?? [],
  options: material.options?.map(mapMaterialOption),
});

export const materialApi = {
  getMaterials: async (params?: GetMaterialsParams) => {
    const { data } = await apiClient.get<PaginatedResponse<ApiMaterial>>(API_ENDPOINTS.MATERIALS.LIST, {
      params: {
        page: params?.page || 1,
        perPage: params?.perPage || 20,
        search: params?.search,
      },
    });

    return {
      ...data,
      data: {
        ...data.data,
        items: data.data.items.map(mapMaterial),
      },
    };
  },

  getMaterial: async (id: number | string) => {
    const { data } = await apiClient.get<ApiDataResponse<ApiMaterial>>(API_ENDPOINTS.MATERIALS.BY_ID(id));

    return {
      ...data,
      data: mapMaterial(data.data),
    };
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

  getMaterialOption: async (
    materialId: number | string,
    optionId: number | string,
  ): Promise<MaterialOption> => {
    const { data } = await apiClient.get<ApiDataResponse<ApiMaterialOption>>(
      API_ENDPOINTS.MATERIALS.OPTION_DETAIL(materialId, optionId),
    );

    return mapMaterialOption(data.data);
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
