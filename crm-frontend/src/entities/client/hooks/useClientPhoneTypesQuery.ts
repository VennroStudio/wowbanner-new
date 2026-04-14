import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/api';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useClientPhoneTypesQuery = (options?: Options) => {
  return useQuery({
    queryKey: ['clientPhoneTypes'],
    queryFn: () => clientApi.getClientPhoneTypes(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
