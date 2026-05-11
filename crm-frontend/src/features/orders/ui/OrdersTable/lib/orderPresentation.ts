import type { Order, OrderItem, OrderItemMilling, OrderNotification, OrderService } from '@/entities/order';

const PRINT_TYPE_COLORS = [
  'bg-emerald-500',
  'bg-sky-500',
  'bg-amber-500',
  'bg-rose-500',
  'bg-violet-500',
  'bg-cyan-500',
  'bg-lime-500',
];

const SERVICE_COLORS = [
  'bg-slate-700',
  'bg-pink-600',
  'bg-indigo-600',
  'bg-teal-600',
  'bg-orange-600',
  'bg-cyan-700',
];

const pluralize = (value: number, forms: [string, string, string]) => {
  const mod10 = value % 10;
  const mod100 = value % 100;

  if (mod10 === 1 && mod100 !== 11) return forms[0];
  if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) return forms[1];
  return forms[2];
};

export const formatOrderDateTime = (value: string) => {
  const date = new Date(value.replace(' ', 'T'));

  if (Number.isNaN(date.getTime())) {
    return value;
  }

  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = String(date.getFullYear()).slice(-2);
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');

  return `${day}.${month}.${year} ${hours}:${minutes}`;
};

export const getRemainingLabel = (deadline: string) => {
  const deadlineDate = new Date(deadline.replace(' ', 'T'));
  const now = new Date();

  if (Number.isNaN(deadlineDate.getTime())) {
    return { label: '—', overdue: false };
  }

  let diff = deadlineDate.getTime() - now.getTime();

  if (diff <= 0) {
    return { label: 'просрочен', overdue: true };
  }

  const minutes = Math.floor(diff / (1000 * 60));
  const days = Math.floor(minutes / (60 * 24));
  diff -= days * 24 * 60 * 1000;

  const hours = Math.floor(diff / (1000 * 60 * 60));
  diff -= hours * 60 * 60 * 1000;

  const remainMinutes = Math.floor(diff / (1000 * 60));

  const parts: string[] = [];

  if (days > 0) {
    parts.push(`${days} ${pluralize(days, ['день', 'дня', 'дней'])}`);
  }

  if (hours > 0) {
    parts.push(`${hours} ${pluralize(hours, ['час', 'часа', 'часов'])}`);
  }

  if (remainMinutes > 0 || parts.length === 0) {
    parts.push(`${remainMinutes} ${pluralize(remainMinutes, ['минута', 'минуты', 'минут'])}`);
  }

  return {
    label: parts.join(' '),
    overdue: false,
  };
};

export const getPrintTypeDots = (order: Order) => {
  const map = new Map<number, { id: number; label: string; short: string; colorClass: string }>();
  const pushItem = (id: number) => {
    if (map.has(id)) return;
    const colorClass = PRINT_TYPE_COLORS[map.size % PRINT_TYPE_COLORS.length];
    map.set(id, {
      id,
      label: `Печать #${id}`,
      short: String(id),
      colorClass,
    });
  };

  order.items.forEach((item) => pushItem(item.print_id));
  order.millings.forEach((item) => pushItem(item.print_id));

  return Array.from(map.values());
};

export const getMaterialLabels = (order: Order) => {
  const labels = new Set<string>();

  order.items.forEach((item: OrderItem) => {
    labels.add(`Материал #${item.material_id} / Опция #${item.option_id}`);
  });

  order.millings.forEach((item: OrderItemMilling) => {
    labels.add(item.material);
  });

  return Array.from(labels);
};

export const getServiceBadges = (services: OrderService[]) => {
  return services.map((service, index) => ({
    id: service.id,
    label: service.service_type.label,
    colorClass: SERVICE_COLORS[index % SERVICE_COLORS.length],
  }));
};

export const getNotificationBadges = (notifications: OrderNotification[]) => {
  return notifications.map((notification) => ({
    id: notification.id,
    label: notification.notification_type.label,
    short: notification.notification_type.label.slice(0, 1).toUpperCase(),
  }));
};
