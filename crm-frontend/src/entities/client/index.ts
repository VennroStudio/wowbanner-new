export type {
  Client,
  ClientEnumOption,
  ClientPhone,
  ClientCompany,
  GetClientsParams,
  PaginatedResponse,
} from './model/types';

export { clientApi } from './api/client.api';
export type { CreateClientBody, UpdateClientBody, CreateClientPhone, CreateClientCompany } from './api/client.api';
export { clientKeys } from './model/query-keys';

export { useClientsQuery } from './model/useClientsQuery';
export { useClientQuery } from './model/useClientQuery';
export { useCreateClientCommand } from './model/useCreateClientCommand';
export { useUpdateClientCommand } from './model/useUpdateClientCommand';
export { useDeleteClientCommand } from './model/useDeleteClientCommand';
export { useClientTypesQuery } from './model/useClientTypesQuery';
export { useClientDocsTypesQuery } from './model/useClientDocsTypesQuery';
export { useClientPhoneTypesQuery } from './model/useClientPhoneTypesQuery';
