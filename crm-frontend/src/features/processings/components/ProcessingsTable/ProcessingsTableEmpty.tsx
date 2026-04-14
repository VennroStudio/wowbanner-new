import { Hammer } from 'lucide-react';
import { PROCESSINGS_TABLE_COLUMN_COUNT } from './constants';

export const ProcessingsTableEmpty = () => (
  <tr>
    <td colSpan={PROCESSINGS_TABLE_COLUMN_COUNT} className="px-6 py-16 text-center">
      <Hammer size={36} className="mx-auto text-slate-200 mb-3" />
      <p className="text-sm font-medium text-slate-600">Обработки не найдены</p>
      <p className="text-xs text-slate-400 mt-1">Измените поиск или создайте новую.</p>
    </td>
  </tr>
);
