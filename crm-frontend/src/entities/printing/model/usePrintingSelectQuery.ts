import { useQuery } from '@tanstack/react-query';
import { printingApi } from '../api/printing.api';
import { printingKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const usePrintingSelectQuery = (options?: Options) => {
  return useQuery({
    queryKey: printingKeys.select(),
    queryFn: () => printingApi.getPrintingSelectOptions(),
    staleTime: STALE_MS,
    enabled: options?.enabled ?? true,
  });
};
