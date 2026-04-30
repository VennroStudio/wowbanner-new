import { useMutation } from '@tanstack/react-query';
import { userApi } from '@/entities/user';
import { parseJwtId } from '@/shared/utils';
import { sessionApi } from '../api/session.api';
import { useSessionStore } from './useSessionStore';

export const useRefreshSession = () => {
  const { setAccessToken, setUser } = useSessionStore();

  return useMutation({
    mutationFn: async () => {
      const response = await sessionApi.refresh();
      const { access_token } = response.data.data;

      const userId = parseJwtId(access_token);
      if (!userId) throw new Error('Invalid token');

      setAccessToken(access_token);
      const userResponse = await userApi.getUser(userId);
      setUser(userResponse.data.data);
      return access_token;
    },
  });
};
