import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/client.api';
import { clientKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useClientDocsTypesQuery = (options?: Options) => {
  return useQuery({
    queryKey: clientKeys.docsTypes(),
    queryFn: () => clientApi.getClientDocsTypes(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
