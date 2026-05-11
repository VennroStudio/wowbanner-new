export type OrderTableColumnKey =
  | 'id'
  | 'notifications'
  | 'status'
  | 'price'
  | 'customer'
  | 'description'
  | 'manager'
  | 'designer'
  | 'remaining'
  | 'printTypes'
  | 'materials'
  | 'services'
  | 'acceptedAt'
  | 'deadlineAt';

export interface OrderTableColumnDefinition {
  key: OrderTableColumnKey;
  label: string;
}

export const ORDER_TABLE_COLUMNS: OrderTableColumnDefinition[] = [
  { key: 'id', label: '№' },
  { key: 'notifications', label: 'Уведомления' },
  { key: 'status', label: 'Статус' },
  { key: 'price', label: 'Цена' },
  { key: 'customer', label: 'Заказчик' },
  { key: 'description', label: 'Описание' },
  { key: 'manager', label: 'Менеджер' },
  { key: 'designer', label: 'Дизайнер' },
  { key: 'remaining', label: 'Осталось' },
  { key: 'printTypes', label: 'Тип печати' },
  { key: 'materials', label: 'Материал' },
  { key: 'services', label: 'Услуги' },
  { key: 'acceptedAt', label: 'Дата постановки' },
  { key: 'deadlineAt', label: 'Дата сдачи' },
];

export const DEFAULT_VISIBLE_ORDER_COLUMNS: Record<OrderTableColumnKey, boolean> = {
  id: true,
  notifications: true,
  status: true,
  price: true,
  customer: true,
  description: true,
  manager: true,
  designer: true,
  remaining: true,
  printTypes: true,
  materials: true,
  services: true,
  acceptedAt: true,
  deadlineAt: true,
};
