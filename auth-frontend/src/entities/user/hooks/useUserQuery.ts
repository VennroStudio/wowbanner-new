import { useQuery } from '@tanstack/react-query';
import { userApi } from '../api/api';

export const useUserQuery = (userId: number) => {
  return useQuery({
    queryKey: ['user', userId],
    queryFn: async () => {
      const response = await userApi.getUser(userId);
      return response.data.data;
    },
    enabled: !!userId,
  });
};
