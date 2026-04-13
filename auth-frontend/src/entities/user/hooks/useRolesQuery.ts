import { useQuery } from '@tanstack/react-query';
import { userApi } from '../api/api';

export const useRolesQuery = () => {
  return useQuery({
    queryKey: ['roles'],
    queryFn: async () => {
      const response = await userApi.getRoles();
      return response.data.data;
    },
  });
};
