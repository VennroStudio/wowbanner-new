import { Printer } from 'lucide-react';
import { PRINTINGS_TABLE_COLUMN_COUNT } from './constants';

export const PrintingsTableEmpty = () => (
  <tr>
    <td colSpan={PRINTINGS_TABLE_COLUMN_COUNT} className="px-6 py-16 text-center">
      <Printer size={36} className="mx-auto text-slate-200 mb-3" />
      <p className="text-sm font-medium text-slate-600">Типы печати не найдены</p>
      <p className="text-xs text-slate-400 mt-1">Измените поиск или создайте новый тип.</p>
    </td>
  </tr>
);
