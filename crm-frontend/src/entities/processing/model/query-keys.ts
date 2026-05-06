export const processingKeys = {
  all: ['processings'] as const,
  lists: () => ['processings'] as const,
  list: (params: { page: number; perPage: number; search: string }) =>
    ['processings', params] as const,
  details: () => ['processing'] as const,
  detail: (id: number | string) => ['processing', id] as const,
  types: () => ['processingTypes'] as const,
  select: () => ['processingSelect'] as const,
};
