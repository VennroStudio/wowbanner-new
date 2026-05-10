import type { Order } from '@/entities/order';
import { RowActionButtons } from '@/shared/ui';

interface OrderTableRowProps {
  order: Order;
  onEdit?: (order: Order) => void;
  onDelete?: (order: Order) => void;
}

export const OrderTableRow = ({ order, onEdit, onDelete }: OrderTableRowProps) => (
  <tr className="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors group">
    <td className="px-5 py-4 align-top">
      <span className="text-xs text-slate-400 font-mono">#{order.id}</span>
    </td>

    <td className="px-5 py-4 align-top">
      <button type="button" onClick={() => onEdit?.(order)} className="text-left w-full">
        <span className="block font-medium text-slate-900 text-sm leading-snug group-hover:text-blue-600 transition-colors">
          Клиент #{order.client_id}
        </span>
      </button>
      <div className="mt-1 space-y-1 text-xs text-slate-500">
        <div>Менеджер: {order.manager_id ? `#${order.manager_id}` : '—'}</div>
        <div>Дизайнер: {order.designer_id ? `#${order.designer_id}` : '—'}</div>
      </div>
    </td>

    <td className="px-5 py-4 align-top">
      <div className="flex flex-col gap-1.5">
        <span className="inline-flex w-fit items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs text-blue-700">
          {order.status_type.label}
        </span>
        <span className="inline-flex w-fit items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">
          {order.storage_type.label}
        </span>
      </div>
    </td>

    <td className="px-5 py-4 align-top">
      <div className="text-sm text-slate-700">
        <div>{order.accepted_at}</div>
        <div className="text-slate-400">до {order.deadline_at}</div>
      </div>
    </td>

    <td className="px-5 py-4 align-top">
      <div className="text-sm font-semibold text-slate-900">{order.price} ₽</div>
      <div className="mt-1 text-xs text-slate-500">
        Услуг: {order.services.length}
      </div>
    </td>

    <td className="px-5 py-4 align-top">
      <div className="flex flex-wrap gap-1.5">
        <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">
          Позиции: {order.items.length}
        </span>
        <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">
          Фрезеровка: {order.millings.length}
        </span>
        <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">
          Файлы: {order.files.length}
        </span>
      </div>
    </td>

    <td className="px-5 py-4 align-top">
      <RowActionButtons onEdit={() => onEdit?.(order)} onDelete={() => onDelete?.(order)} />
    </td>
  </tr>
);
