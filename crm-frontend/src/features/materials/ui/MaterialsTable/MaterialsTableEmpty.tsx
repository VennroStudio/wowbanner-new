import { Package } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { MATERIALS_TABLE_COLUMN_COUNT } from './constants';

export const MaterialsTableEmpty = () => (
  <TableStateRow
    colSpan={MATERIALS_TABLE_COLUMN_COUNT}
    icon={<Package size={36} />}
    title="Материалы не найдены"
    description="Измените поиск или создайте новый материал"
  />
);
