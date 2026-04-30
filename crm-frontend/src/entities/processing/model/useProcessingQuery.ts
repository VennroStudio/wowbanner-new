import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import { processingKeys } from './query-keys';

type ProcessingResponse = Awaited<ReturnType<typeof processingApi.getProcessing>>;

export const useProcessingQuery = (
  id: number | string,
  options?: Pick<UseQueryOptions<ProcessingResponse>, 'enabled'>,
) => {
  const idNum = typeof id === 'string' ? Number(id) : id;
  const hasId = idNum > 0 && !Number.isNaN(idNum);

  return useQuery({
    queryKey: processingKeys.detail(id),
    queryFn: () => processingApi.getProcessing(id),
    enabled: (options?.enabled ?? true) && hasId,
  });
};
