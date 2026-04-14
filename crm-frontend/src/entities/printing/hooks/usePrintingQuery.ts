import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { printingApi } from '../api/api';

type PrintingResponse = Awaited<ReturnType<typeof printingApi.getPrinting>>;

export const usePrintingQuery = (
  id: number | string,
  options?: Pick<UseQueryOptions<PrintingResponse>, 'enabled'>,
) => {
  const idNum = typeof id === 'string' ? Number(id) : id;
  const hasId = idNum > 0 && !Number.isNaN(idNum);

  return useQuery({
    queryKey: ['printing', id],
    queryFn: () => printingApi.getPrinting(id),
    enabled: (options?.enabled ?? true) && hasId,
  });
};
