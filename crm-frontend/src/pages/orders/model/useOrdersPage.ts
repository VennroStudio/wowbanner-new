import type { Order } from '@/entities/order';
import type { OrdersFilterValues } from '@/features/orders';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';
import { useState } from 'react';

const initialFilters: OrdersFilterValues = {
  dateFrom: '',
  dateTo: '',
  printId: '',
  materialId: '',
  optionId: '',
  docs: '',
  managerId: '',
  designerId: '',
  statusType: '',
  storageType: '',
  serviceType: '',
  archived: false,
  deleted: false,
};

export const useOrdersPage = () => {
  const crud = useCrudListPageState<Order>();
  const [filters, setFilters] = useState<OrdersFilterValues>(initialFilters);

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

  const resetFilters = () => {
    setFilters(initialFilters);
    crud.setPage(1);
  };

  return {
    ...crud,
    filters,
    setFilter,
    resetFilters,
  };
};
