export const clientKeys = {
  all: ['clients'] as const,
  lists: () => ['clients'] as const,
  list: (params: { page: number; perPage: number; search: string }) =>
    ['clients', params] as const,
  details: () => ['client'] as const,
  detail: (id: number | string) => ['client', id] as const,
  types: () => ['clientTypes'] as const,
  docsTypes: () => ['clientDocsTypes'] as const,
  phoneTypes: () => ['clientPhoneTypes'] as const,
};
