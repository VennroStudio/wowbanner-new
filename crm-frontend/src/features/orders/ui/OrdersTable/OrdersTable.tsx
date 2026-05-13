import type { Order } from '@/entities/order';
import { PaginationBar } from '@/shared/ui';
import { ORDER_TABLE_COLUMNS, type OrderTableColumnKey } from '../../model/orderTableColumns';
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
  visibleColumns: Record<OrderTableColumnKey, boolean>;
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
  visibleColumns,
  onPageChange,
  onEdit,
  onDelete,
}: OrdersTableProps) => {
  const totalPages = total ? Math.ceil(total / perPage) : 1;
  const hasData = !isLoading && !isError && orders && orders.length > 0;
  const visibleHeaders = ORDER_TABLE_COLUMNS.filter((column) => visibleColumns[column.key]);

  return (
    <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[420px]">
      <div className="overflow-x-auto flex-1">
        <table className="w-full text-left border-collapse min-w-[1440px]">
          <thead>
            <tr className="bg-slate-800 border-b border-slate-700">
              {visibleHeaders.map((column) => (
                <th key={column.key} className="px-4 py-3 text-[11px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                  {column.label}
                </th>
              ))}
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
                  visibleColumns={visibleColumns}
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
