import { useMutation, useQueryClient } from '@tanstack/react-query';
import { productApi } from '../api/product.api';
import { productKeys } from './query-keys';

export const useDeleteProductCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => productApi.deleteProduct(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: productKeys.lists() });
    },
  });
};
