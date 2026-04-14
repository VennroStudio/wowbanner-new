import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/api';

const STALE_MS = 1000 * 60 * 60; // справочник enum меняется редко

type Options = { enabled?: boolean };

export const useClientTypesQuery = (options?: Options) => {
  return useQuery({
    queryKey: ['clientTypes'],
    queryFn: () => clientApi.getClientTypes(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
