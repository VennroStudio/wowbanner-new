import { PRINTINGS_TABLE_COLUMN_COUNT } from './constants';

export const PrintingsTableError = () => (
  <tr>
    <td
      colSpan={PRINTINGS_TABLE_COLUMN_COUNT}
      className="px-6 py-12 text-center text-sm text-red-600"
    >
      Не удалось загрузить список. Попробуйте обновить страницу.
    </td>
  </tr>
);
