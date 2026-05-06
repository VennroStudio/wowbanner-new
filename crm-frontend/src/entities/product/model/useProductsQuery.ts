import { useQuery } from '@tanstack/react-query';
import { productApi } from '../api/product.api';
import type { GetProductsParams } from './types';
import { productKeys } from './query-keys';

export const useProductsQuery = (params?: GetProductsParams) => {
  const page = params?.page || 1;
  const perPage = params?.perPage || 20;
  const search = params?.search || '';

  return useQuery({
    queryKey: productKeys.list({ page, perPage, search }),
    queryFn: () => productApi.getProducts({ page, perPage, search }),
  });
};
