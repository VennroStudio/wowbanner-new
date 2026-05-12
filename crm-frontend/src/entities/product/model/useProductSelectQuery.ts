import { useQuery } from '@tanstack/react-query';
import { productApi } from '../api/product.api';
import { productKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useProductSelectQuery = (
  printId?: number | string,
  options?: Options,
) => {
  return useQuery({
    queryKey: productKeys.select(printId),
    queryFn: () => productApi.getProductSelectOptions({ printId: Number(printId) || undefined }),
    staleTime: STALE_MS,
    enabled: (options?.enabled ?? true) && Number(printId) > 0,
  });
};
