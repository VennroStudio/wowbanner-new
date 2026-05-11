import { useQuery } from '@tanstack/react-query';
import { userApi } from '../api/user.api';
import { userKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useUserSelectQuery = (role?: number, options?: Options) => {
  return useQuery({
    queryKey: userKeys.select(role),
    queryFn: () => userApi.getUserSelectOptions(role),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
