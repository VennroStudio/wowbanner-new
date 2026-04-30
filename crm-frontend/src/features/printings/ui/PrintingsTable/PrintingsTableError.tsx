import { AlertCircle } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { PRINTINGS_TABLE_COLUMN_COUNT } from './constants';

export const PrintingsTableError = () => (
  <TableStateRow
    colSpan={PRINTINGS_TABLE_COLUMN_COUNT}
    icon={<AlertCircle size={32} />}
    title="Ошибка загрузки типов печати"
    description="Не удалось загрузить список. Попробуйте обновить страницу."
    tone="error"
  />
);
