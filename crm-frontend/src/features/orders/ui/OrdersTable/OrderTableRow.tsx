import type { Order } from '@/entities/order';
import { RowActionButtons } from '@/shared/ui';
import type { OrderTableColumnKey } from '../../model/orderTableColumns';
import {
  formatOrderDateTime,
  getMaterialLabels,
  getNotificationBadges,
  getPrintTypeDots,
  getRemainingLabel,
  getServiceBadges,
} from './lib/orderPresentation';

interface OrderTableRowProps {
  order: Order;
  visibleColumns: Record<OrderTableColumnKey, boolean>;
  onEdit?: (order: Order) => void;
  onDelete?: (order: Order) => void;
}

export const OrderTableRow = ({
  order,
  visibleColumns,
  onEdit,
  onDelete,
}: OrderTableRowProps) => {
  const remaining = getRemainingLabel(order.deadline_at);
  const printDots = getPrintTypeDots(order);
  const materialLabels = getMaterialLabels(order);
  const serviceBadges = getServiceBadges(order.services);
  const notificationBadges = getNotificationBadges(order.notifications);

  return (
    <tr className="border-b border-amber-100 last:border-0 bg-amber-50/50 hover:bg-amber-50 transition-colors">
      {visibleColumns.id ? (
        <td className="px-4 py-4 align-top">
          <span className="text-sm font-semibold text-orange-500">#{order.id}</span>
        </td>
      ) : null}

      {visibleColumns.notifications ? (
        <td className="px-4 py-4 align-top">
          <div className="flex flex-wrap gap-1.5">
            {notificationBadges.length === 0 ? (
              <span className="text-xs text-slate-400">—</span>
            ) : (
              notificationBadges.map((item) => (
                <span
                  key={item.id}
                  title={item.label}
                  className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-[11px] font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200"
                >
                  {item.short}
                </span>
              ))
            )}
          </div>
        </td>
      ) : null}

      {visibleColumns.status ? (
        <td className="px-4 py-4 align-top">
          <div className="space-y-2">
            <span className="inline-flex rounded-lg bg-sky-700 px-3 py-1.5 text-xs font-semibold text-white">
              {order.status_type.label}
            </span>
            <div className="text-xs text-slate-500">{order.storage_type.label}</div>
          </div>
        </td>
      ) : null}

      {visibleColumns.price ? (
        <td className="px-4 py-4 align-top">
          <div className="text-sm font-semibold text-slate-900">{order.price} ₽</div>
        </td>
      ) : null}

      {visibleColumns.customer ? (
        <td className="px-4 py-4 align-top">
          <div className="text-sm font-medium text-slate-900 leading-6">{order.client?.name ?? `Клиент #${order.client_id}`}</div>
        </td>
      ) : null}

      {visibleColumns.description ? (
        <td className="px-4 py-4 align-top">
          <div className="max-w-[280px] whitespace-pre-line text-sm leading-6 text-slate-700">
            {order.general_note || '—'}
          </div>
        </td>
      ) : null}

      {visibleColumns.manager ? (
        <td className="px-4 py-4 align-top">
          <div className="text-sm text-slate-700">{order.manager?.name ?? '—'}</div>
        </td>
      ) : null}

      {visibleColumns.designer ? (
        <td className="px-4 py-4 align-top">
          <div className="text-sm text-slate-700">{order.designer?.name ?? '—'}</div>
        </td>
      ) : null}

      {visibleColumns.remaining ? (
        <td className="px-4 py-4 align-top">
          <div className={`text-sm font-medium ${remaining.overdue ? 'text-red-600' : 'text-emerald-700'}`}>
            {remaining.label}
          </div>
        </td>
      ) : null}

      {visibleColumns.printTypes ? (
        <td className="px-4 py-4 align-top">
          <div className="flex flex-wrap gap-2">
            {printDots.length === 0 ? (
              <span className="text-xs text-slate-400">—</span>
            ) : (
              printDots.map((printType) => (
                <span
                  key={printType.id}
                  title={printType.label}
                  className={`inline-flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-bold text-white ${printType.colorClass}`}
                >
                  {printType.short}
                </span>
              ))
            )}
          </div>
        </td>
      ) : null}

      {visibleColumns.materials ? (
        <td className="px-4 py-4 align-top">
          <div className="space-y-1 text-sm text-slate-700">
            {materialLabels.length === 0 ? (
              <span className="text-xs text-slate-400">—</span>
            ) : (
              materialLabels.slice(0, 3).map((label) => (
                <div key={label} className="max-w-[220px] truncate">
                  {label}
                </div>
              ))
            )}
          </div>
        </td>
      ) : null}

      {visibleColumns.services ? (
        <td className="px-4 py-4 align-top">
          <div className="flex flex-wrap gap-2">
            {serviceBadges.length === 0 ? (
              <span className="text-xs text-slate-400">—</span>
            ) : (
              serviceBadges.map((service) => (
                <span
                  key={service.id}
                  className={`inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold text-white ${service.colorClass}`}
                >
                  {service.label}
                </span>
              ))
            )}
          </div>
        </td>
      ) : null}

      {visibleColumns.acceptedAt ? (
        <td className="px-4 py-4 align-top">
          <div className="text-sm text-slate-700">{formatOrderDateTime(order.accepted_at)}</div>
        </td>
      ) : null}

      {visibleColumns.deadlineAt ? (
        <td className="px-4 py-4 align-top">
          <div className="text-sm font-medium text-slate-800">{formatOrderDateTime(order.deadline_at)}</div>
        </td>
      ) : null}

      {visibleColumns.actions ? (
        <td className="px-4 py-4 align-top">
          <RowActionButtons onEdit={() => onEdit?.(order)} onDelete={() => onDelete?.(order)} />
        </td>
      ) : null}
    </tr>
  );
};
