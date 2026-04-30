import { Hammer } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { PROCESSINGS_TABLE_COLUMN_COUNT } from './constants';

export const ProcessingsTableEmpty = () => (
  <TableStateRow
    colSpan={PROCESSINGS_TABLE_COLUMN_COUNT}
    icon={<Hammer size={36} />}
    title="Обработки не найдены"
    description="Измените поиск или создайте новую."
  />
);
