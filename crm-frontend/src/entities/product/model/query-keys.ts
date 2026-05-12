export const productKeys = {
  all: ['products'] as const,
  lists: () => ['products'] as const,
  list: (params: { page: number; perPage: number; search: string }) =>
    ['products', params] as const,
  select: (printId?: number | string) => ['productSelect', printId ?? 'all'] as const,
  details: () => ['product'] as const,
  detail: (id: number | string) => ['product', id] as const,
};
