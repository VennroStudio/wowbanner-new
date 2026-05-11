import type { Order } from '@/entities/order';
import type { OrdersFilterValues } from '@/features/orders';
import type { OrderTableColumnKey } from '@/features/orders';
import { DEFAULT_VISIBLE_ORDER_COLUMNS } from '@/features/orders/model/orderTableColumns';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';
import { useEffect, useState } from 'react';

const initialFilters: OrdersFilterValues = {
  dateFrom: '',
  dateTo: '',
  printIds: [],
  materialId: '',
  optionId: '',
  docs: '',
  managerId: '',
  designerId: '',
  statusTypes: [],
  storageType: '',
  serviceType: '',
  archived: false,
  deleted: false,
};

const STORAGE_KEY = 'orders-table-visible-columns';

export const useOrdersPage = () => {
  const crud = useCrudListPageState<Order>();
  const [filters, setFilters] = useState<OrdersFilterValues>(initialFilters);
  const [visibleColumns, setVisibleColumns] = useState<Record<OrderTableColumnKey, boolean>>(() => {
    const raw = window.localStorage.getItem(STORAGE_KEY);

    if (!raw) {
      return DEFAULT_VISIBLE_ORDER_COLUMNS;
    }

    try {
      const parsed = JSON.parse(raw) as Partial<Record<OrderTableColumnKey, boolean>>;
      return {
        ...DEFAULT_VISIBLE_ORDER_COLUMNS,
        ...parsed,
      };
    } catch {
      window.localStorage.removeItem(STORAGE_KEY);
      return DEFAULT_VISIBLE_ORDER_COLUMNS;
    }
  });

  useEffect(() => {
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(visibleColumns));
  }, [visibleColumns]);

  const setFilter = <K extends keyof OrdersFilterValues>(key: K, value: OrdersFilterValues[K]) => {
    setFilters((prev) => {
      const next = { ...prev, [key]: value };
      if (key === 'materialId' && prev.materialId !== value) {
        next.optionId = '';
      }
      return next;
    });
    crud.setPage(1);
  };

  const toggleColumn = (key: OrderTableColumnKey) => {
    setVisibleColumns((current) => ({
      ...current,
      [key]: !current[key],
    }));
  };

  const resetFilters = () => {
    setFilters(initialFilters);
    crud.setPage(1);
  };

  return {
    ...crud,
    filters,
    setFilter,
    resetFilters,
    visibleColumns,
    toggleColumn,
  };
};
