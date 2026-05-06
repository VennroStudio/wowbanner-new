import { useQuery } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import { materialKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

type Options = { enabled?: boolean };

export const useMaterialOptionSelectQuery = (
  materialId: number | string,
  options?: Options,
) => {
  return useQuery({
    queryKey: materialKeys.optionSelect(materialId),
    queryFn: () => materialApi.getMaterialOptionSelectOptions(materialId),
    staleTime: STALE_MS,
    enabled: (options?.enabled ?? true) && Number(materialId) > 0,
  });
};
