import { AlertCircle } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { CLIENTS_TABLE_COLUMN_COUNT } from './constants';

export const ClientsTableError = () => (
  <TableStateRow
    colSpan={CLIENTS_TABLE_COLUMN_COUNT}
    icon={<AlertCircle size={32} />}
    title="Ошибка загрузки клиентов"
    description="Попробуйте обновить страницу"
    tone="error"
  />
);
