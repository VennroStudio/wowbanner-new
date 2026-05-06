import { useMutation, useQueryClient } from '@tanstack/react-query';
import { productApi } from '../api/product.api';
import type { CreateProductBody } from '../api/product.api';
import { productKeys } from './query-keys';

export const useCreateProductCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateProductBody) => productApi.createProduct(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: productKeys.lists() });
    },
  });
};
