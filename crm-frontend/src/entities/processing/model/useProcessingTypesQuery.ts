import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import { processingKeys } from './query-keys';

type TypesResponse = Awaited<ReturnType<typeof processingApi.getProcessingTypes>>;

export const useProcessingTypesQuery = (
  options?: Pick<UseQueryOptions<TypesResponse>, 'enabled'>,
) => {
  return useQuery({
    queryKey: processingKeys.types(),
    queryFn: () => processingApi.getProcessingTypes(),
    staleTime: 5 * 60 * 1000,
    enabled: options?.enabled ?? true,
  });
};
