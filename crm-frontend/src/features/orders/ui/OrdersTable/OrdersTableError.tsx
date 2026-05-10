import { TableStateRow } from '@/shared/ui';

export const OrdersTableError = () => (
  <TableStateRow colSpan={7} title="Не удалось загрузить заказы." tone="error" />
);
