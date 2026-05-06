import { useMutation, useQueryClient } from '@tanstack/react-query';
import { productApi } from '../api/product.api';
import type { UpdateProductBody } from '../api/product.api';
import { productKeys } from './query-keys';

export const useUpdateProductCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateProductBody }) =>
      productApi.updateProduct(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: productKeys.lists() });
      queryClient.invalidateQueries({ queryKey: productKeys.detail(id) });
    },
  });
};
