import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { processingApi } from '../api/api';

type TypesResponse = Awaited<ReturnType<typeof processingApi.getProcessingTypes>>;

export const useProcessingTypesQuery = (
  options?: Pick<UseQueryOptions<TypesResponse>, 'enabled'>,
) => {
  return useQuery({
    queryKey: ['processingTypes'] as const,
    queryFn: () => processingApi.getProcessingTypes(),
    staleTime: 5 * 60 * 1000,
    enabled: options?.enabled ?? true,
  });
};
