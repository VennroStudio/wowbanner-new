import type { Client } from '@/entities/client';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';

export const useClientsPage = () =>
  useCrudListPageState<Client>({
    searchDelay: 800,
  });
