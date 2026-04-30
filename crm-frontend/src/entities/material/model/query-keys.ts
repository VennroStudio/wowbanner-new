export const materialKeys = {
  all: ['materials'] as const,
  lists: () => ['materials'] as const,
  list: (params: { page: number; perPage: number; search: string }) =>
    ['materials', params] as const,
  details: () => ['material'] as const,
  detail: (id: number | string) => ['material', id] as const,
};
