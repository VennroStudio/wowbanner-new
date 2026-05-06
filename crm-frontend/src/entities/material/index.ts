export type {
  Material,
  MaterialCreateUpdateOption,
  MaterialEnumRef,
  MaterialImage,
  MaterialOption,
  MaterialOptionProcessing,
  MaterialPricingByAreaRow,
  MaterialPricingByCutRow,
  MaterialPricingByPieceRow,
  GetMaterialsParams,
  PaginatedResponse,
} from './model/types';

export { materialApi } from './api/material.api';
export type { CreateMaterialBody, UpdateMaterialBody } from './api/material.api';
export { materialKeys } from './model/query-keys';

export { useMaterialsQuery } from './model/useMaterialsQuery';
export { useMaterialQuery } from './model/useMaterialQuery';
export { useMaterialOptionPricingTypesQuery } from './model/useMaterialOptionPricingTypesQuery';
export { useMaterialAreaRangeTypesQuery } from './model/useMaterialAreaRangeTypesQuery';
export { useMaterialDpiTypesQuery } from './model/useMaterialDpiTypesQuery';
export { useMaterialVariantTypesQuery } from './model/useMaterialVariantTypesQuery';
export { useMaterialPricingCutTypesQuery } from './model/useMaterialPricingCutTypesQuery';
export { useCreateMaterialCommand } from './model/useCreateMaterialCommand';
export { useUpdateMaterialCommand } from './model/useUpdateMaterialCommand';
export { useDeleteMaterialCommand } from './model/useDeleteMaterialCommand';
export { useUploadMaterialImagesCommand } from './model/useUploadMaterialImagesCommand';
export { useUpdateMaterialImageAltCommand } from './model/useUpdateMaterialImageAltCommand';
export { useDeleteMaterialImageCommand } from './model/useDeleteMaterialImageCommand';
