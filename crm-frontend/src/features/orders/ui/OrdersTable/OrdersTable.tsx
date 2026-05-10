import type { Order } from '@/entities/order';
import { PaginationBar } from '@/shared/ui';
import { OrderTableRow } from './OrderTableRow';
import { OrdersTableSkeleton } from './OrdersTableSkeleton';
import { OrdersTableEmpty } from './OrdersTableEmpty';
import { OrdersTableError } from './OrdersTableError';

interface OrdersTableProps {
  orders: Order[] | undefined;
  total: number | undefined;
  isLoading: boolean;
  isError: boolean;
  page: number;
  perPage: number;
  onPageChange: (page: number) => void;
  onEdit?: (order: Order) => void;
  onDelete?: (order: Order) => void;
}

export const OrdersTable = ({
  orders,
  total,
  isLoading,
  isError,
  page,
  perPage,
  onPageChange,
  onEdit,
  onDelete,
}: OrdersTableProps) => {
  const totalPages = total ? Math.ceil(total / perPage) : 1;
  const hasData = !isLoading && !isError && orders && orders.length > 0;

  return (
    <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[420px]">
      <div className="overflow-x-auto flex-1">
        <table className="w-full text-left border-collapse table-fixed">
          <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              <th className="px-5 py-3 w-[80px] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">ID</th>
              <th className="px-5 py-3 w-[19%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Клиент и ответственные</th>
              <th className="px-5 py-3 w-[14%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Статус / склад</th>
              <th className="px-5 py-3 w-[18%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Период</th>
              <th className="px-5 py-3 w-[12%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Сумма</th>
              <th className="px-5 py-3 w-[22%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Состав</th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Действия</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <OrdersTableSkeleton />
            ) : isError ? (
              <OrdersTableError />
            ) : !orders || orders.length === 0 ? (
              <OrdersTableEmpty />
            ) : (
              orders.map((order) => (
                <OrderTableRow
                  key={order.id}
                  order={order}
                  onEdit={onEdit}
                  onDelete={onDelete}
                />
              ))
            )}
          </tbody>
        </table>
      </div>

      {hasData && (
        <PaginationBar
          page={page}
          totalPages={totalPages}
          total={total ?? 0}
          onPageChange={onPageChange}
        />
      )}
    </div>
  );
};
