import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/client.api';
import { clientKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useClientPhoneTypesQuery = (options?: Options) => {
  return useQuery({
    queryKey: clientKeys.phoneTypes(),
    queryFn: () => clientApi.getClientPhoneTypes(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
