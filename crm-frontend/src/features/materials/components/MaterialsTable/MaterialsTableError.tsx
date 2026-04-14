import { MATERIALS_TABLE_COLUMN_COUNT } from './constants';

export const MaterialsTableError = () => (
  <tr>
    <td colSpan={MATERIALS_TABLE_COLUMN_COUNT} className="px-6 py-12 text-center text-sm text-red-600">
      Не удалось загрузить список. Попробуйте обновить страницу.
    </td>
  </tr>
);
