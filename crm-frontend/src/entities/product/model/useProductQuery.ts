import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { productApi } from '../api/product.api';
import { productKeys } from './query-keys';

type ProductResponse = Awaited<ReturnType<typeof productApi.getProduct>>;

export const useProductQuery = (
  id: number | string,
  options?: Pick<UseQueryOptions<ProductResponse>, 'enabled'>,
) => {
  return useQuery({
    queryKey: productKeys.detail(id),
    queryFn: () => productApi.getProduct(id),
    enabled: options?.enabled ?? true,
  });
};
