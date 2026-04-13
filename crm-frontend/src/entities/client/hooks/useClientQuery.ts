import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { clientApi } from '../api/api';

type ClientResponse = Awaited<ReturnType<typeof clientApi.getClient>>;

export const useClientQuery = (
  id: number | string,
  options?: Pick<UseQueryOptions<ClientResponse>, 'enabled'>,
) => {
  const idNum = typeof id === 'string' ? Number(id) : id;
  const hasId = idNum > 0 && !Number.isNaN(idNum);

  return useQuery({
    queryKey: ['client', id],
    queryFn: () => clientApi.getClient(id),
    enabled: (options?.enabled ?? true) && hasId,
  });
};
