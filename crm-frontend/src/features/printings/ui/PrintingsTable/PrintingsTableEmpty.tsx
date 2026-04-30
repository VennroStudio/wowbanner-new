import { Printer } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { PRINTINGS_TABLE_COLUMN_COUNT } from './constants';

export const PrintingsTableEmpty = () => (
  <TableStateRow
    colSpan={PRINTINGS_TABLE_COLUMN_COUNT}
    icon={<Printer size={36} />}
    title="Типы печати не найдены"
    description="Измените поиск или создайте новый тип."
  />
);
