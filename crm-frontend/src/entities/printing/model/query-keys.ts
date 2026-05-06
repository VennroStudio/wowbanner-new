export const printingKeys = {
  all: ['printings'] as const,
  lists: () => ['printings'] as const,
  list: (params: { page: number; perPage: number; search: string }) =>
    ['printings', params] as const,
  details: () => ['printing'] as const,
  detail: (id: number | string) => ['printing', id] as const,
  select: () => ['printingSelect'] as const,
};
