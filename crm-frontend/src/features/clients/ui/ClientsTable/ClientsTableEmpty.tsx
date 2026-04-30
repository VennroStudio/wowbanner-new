import { Users } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { CLIENTS_TABLE_COLUMN_COUNT } from './constants';

export const ClientsTableEmpty = () => (
  <TableStateRow
    colSpan={CLIENTS_TABLE_COLUMN_COUNT}
    icon={<Users size={36} />}
    title="Клиенты не найдены"
    description="Попробуйте изменить запрос или добавьте нового клиента"
  />
);
