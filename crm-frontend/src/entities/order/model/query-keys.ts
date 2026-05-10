export const orderKeys = {
  all: ['orders'] as const,
  lists: () => ['orders'] as const,
  list: (params: Record<string, unknown>) => ['orders', params] as const,
  details: () => ['order'] as const,
  detail: (id: number | string) => ['order', id] as const,
  statusTypes: () => ['orders', 'status-types'] as const,
  storageTypes: () => ['orders', 'storage-types'] as const,
  deliveryTypes: () => ['orders', 'delivery-types'] as const,
  paymentOperationTypes: () => ['orders', 'payment-operation-types'] as const,
  paymentTypes: () => ['orders', 'payment-types'] as const,
  sectionTypes: () => ['orders', 'section-types'] as const,
  serviceTypes: () => ['orders', 'service-types'] as const,
};
