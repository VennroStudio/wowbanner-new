import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/api';
import type { GetClientsParams } from '../model/types';

export const useClientsQuery = (params?: GetClientsParams) => {
  return useQuery({
    queryKey: ['clients', params],
    queryFn: () => clientApi.getClients(params),
    placeholderData: (previousData) => previousData,
  });
};
