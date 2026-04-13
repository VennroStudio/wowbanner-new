import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/api';

export const useClientQuery = (id: number | string) => {
  return useQuery({
    queryKey: ['client', id],
    queryFn: () => clientApi.getClient(id),
    enabled: !!id,
  });
};
