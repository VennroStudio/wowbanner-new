import { Package } from 'lucide-react';
import { MATERIALS_TABLE_COLUMN_COUNT } from './constants';

export const MaterialsTableEmpty = () => (
  <tr>
    <td colSpan={MATERIALS_TABLE_COLUMN_COUNT} className="px-6 py-16 text-center">
      <Package size={36} className="mx-auto text-slate-200 mb-3" />
      <p className="text-sm font-medium text-slate-600">Материалы не найдены</p>
      <p className="text-xs text-slate-400 mt-1">Измените поиск или создайте новый материал</p>
    </td>
  </tr>
);
