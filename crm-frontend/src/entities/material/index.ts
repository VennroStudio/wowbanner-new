export type { Material, MaterialImage, GetMaterialsParams, PaginatedResponse } from './model/types';

export { materialApi } from './api/api';
export type { CreateMaterialBody, UpdateMaterialBody } from './api/api';

export { useMaterialsQuery } from './hooks/useMaterialsQuery';
export { useMaterialQuery } from './hooks/useMaterialQuery';
export { useCreateMaterialCommand } from './hooks/useCreateMaterialCommand';
export { useUpdateMaterialCommand } from './hooks/useUpdateMaterialCommand';
export { useDeleteMaterialCommand } from './hooks/useDeleteMaterialCommand';
export { useUploadMaterialImagesCommand } from './hooks/useUploadMaterialImagesCommand';
export { useUpdateMaterialImageAltCommand } from './hooks/useUpdateMaterialImageAltCommand';
export { useDeleteMaterialImageCommand } from './hooks/useDeleteMaterialImageCommand';
