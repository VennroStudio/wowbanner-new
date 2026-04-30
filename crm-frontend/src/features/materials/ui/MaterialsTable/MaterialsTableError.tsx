import { AlertCircle } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { MATERIALS_TABLE_COLUMN_COUNT } from './constants';

export const MaterialsTableError = () => (
  <TableStateRow
    colSpan={MATERIALS_TABLE_COLUMN_COUNT}
    icon={<AlertCircle size={32} />}
    title="Ошибка загрузки материалов"
    description="Не удалось загрузить список. Попробуйте обновить страницу."
    tone="error"
  />
);
