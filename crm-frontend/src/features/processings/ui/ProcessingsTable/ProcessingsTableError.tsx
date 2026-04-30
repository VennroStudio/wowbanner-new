import { AlertCircle } from 'lucide-react';
import { TableStateRow } from '@/shared/ui';
import { PROCESSINGS_TABLE_COLUMN_COUNT } from './constants';

export const ProcessingsTableError = () => (
  <TableStateRow
    colSpan={PROCESSINGS_TABLE_COLUMN_COUNT}
    icon={<AlertCircle size={32} />}
    title="Ошибка загрузки обработок"
    description="Не удалось загрузить список. Попробуйте обновить страницу."
    tone="error"
  />
);
