import { useQuery } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import { processingKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useProcessingSelectQuery = (options?: Options) => {
  return useQuery({
    queryKey: processingKeys.select(),
    queryFn: () => processingApi.getProcessingSelectOptions(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
