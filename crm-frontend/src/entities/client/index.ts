export type {
  Client,
  ClientEnumOption,
  ClientPhone,
  ClientCompany,
  GetClientsParams,
  PaginatedResponse,
} from './model/types';

export { clientApi } from './api/api';
export type { CreateClientBody, UpdateClientBody, CreateClientPhone, CreateClientCompany } from './api/api';

export { useClientsQuery } from './hooks/useClientsQuery';
export { useClientQuery } from './hooks/useClientQuery';
export { useCreateClientCommand } from './hooks/useCreateClientCommand';
export { useUpdateClientCommand } from './hooks/useUpdateClientCommand';
export { useDeleteClientCommand } from './hooks/useDeleteClientCommand';
export { useClientTypesQuery } from './hooks/useClientTypesQuery';
export { useClientDocsTypesQuery } from './hooks/useClientDocsTypesQuery';
export { useClientPhoneTypesQuery } from './hooks/useClientPhoneTypesQuery';
