import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { materialApi } from '../api/api';

type MaterialResponse = Awaited<ReturnType<typeof materialApi.getMaterial>>;

export const useMaterialQuery = (
  id: number | string,
  options?: Pick<UseQueryOptions<MaterialResponse>, 'enabled'>,
) => {
  const idNum = typeof id === 'string' ? Number(id) : id;
  const hasId = idNum > 0 && !Number.isNaN(idNum);

  return useQuery({
    queryKey: ['material', id],
    queryFn: () => materialApi.getMaterial(id),
    enabled: (options?.enabled ?? true) && hasId,
  });
};
