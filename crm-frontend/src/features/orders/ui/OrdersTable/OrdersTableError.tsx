import { TableStateRow } from '@/shared/ui';

export const OrdersTableError = () => (
  <TableStateRow colSpan={15} title="Не удалось загрузить заказы." tone="error" />
);
