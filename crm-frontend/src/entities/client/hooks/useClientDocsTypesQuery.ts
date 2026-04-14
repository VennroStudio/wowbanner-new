import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/api';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useClientDocsTypesQuery = (options?: Options) => {
  return useQuery({
    queryKey: ['clientDocsTypes'],
    queryFn: () => clientApi.getClientDocsTypes(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
