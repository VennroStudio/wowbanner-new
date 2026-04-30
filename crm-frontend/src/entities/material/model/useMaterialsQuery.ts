import { useQuery } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import type { GetMaterialsParams } from './types';
import { materialKeys } from './query-keys';

export const useMaterialsQuery = (params?: GetMaterialsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';
  const queryParams = { page, perPage, search };

  return useQuery({
    queryKey: materialKeys.list(queryParams),
    queryFn: () => materialApi.getMaterials({ page, perPage, search }),
    placeholderData: (previousData, previousQuery) => {
      if (!previousQuery) return undefined;
      const prevParams = previousQuery.queryKey[1] as {
        page: number;
        perPage: number;
        search: string;
      };
      if (prevParams.search !== search || prevParams.perPage !== perPage) return undefined;
      return previousData;
    },
  });
};
