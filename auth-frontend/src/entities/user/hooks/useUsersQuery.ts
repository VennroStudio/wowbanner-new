import { useQuery } from '@tanstack/react-query';
import { userApi, type UsersListParams } from '../api/api';

export const useUsersQuery = (params?: UsersListParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';

  return useQuery({
    queryKey: ['users', page, perPage, search],
    queryFn: async () => {
      const response = await userApi.getUsers({ page, perPage, search: search || undefined });
      return response.data.data;
    },
  });
};
