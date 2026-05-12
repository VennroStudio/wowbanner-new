export const materialKeys = {
  all: ['materials'] as const,
  lists: () => ['materials'] as const,
  list: (params: { page: number; perPage: number; search: string }) =>
    ['materials', params] as const,
  details: () => ['material'] as const,
  detail: (id: number | string) => ['material', id] as const,
  select: () => ['materialSelect'] as const,
  optionSelect: (materialId: number | string) => ['materialOptionSelect', materialId] as const,
  processingSelect: (materialId: number | string, optionId: number | string) =>
    ['materialProcessingSelect', materialId, optionId] as const,
  optionPricingTypes: () => ['materialOptionPricingTypes'] as const,
  areaRangeTypes: () => ['materialAreaRangeTypes'] as const,
  dpiTypes: () => ['materialDpiTypes'] as const,
  variantTypes: () => ['materialVariantTypes'] as const,
  pricingCutTypes: () => ['materialPricingCutTypes'] as const,
};
