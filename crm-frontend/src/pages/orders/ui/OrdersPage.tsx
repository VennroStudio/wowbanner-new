import {
  useOrdersQuery,
  useOrderStatusTypesQuery,
  useOrderStorageTypesQuery,
  useOrderServiceTypesQuery,
} from '@/entities/order';
import { usePrintingSelectQuery } from '@/entities/printing';
import { useMaterialOptionSelectQuery, useMaterialSelectQuery } from '@/entities/material';
import { useClientDocsTypesQuery } from '@/entities/client';
import { useUserSelectQuery } from '@/entities/user';
import { AlertBanner } from '@/shared/ui';
import { DeleteOrderModal, OrdersFilters, OrdersHeader, OrdersTable } from '@/features/orders';
import { useOrdersPage } from '../model/useOrdersPage';

const toOptionalNumber = (value: string) => (value ? Number(value) : undefined);

export const OrdersPage = () => {
  const perPage = 20;
  const {
    search,
    setSearch,
    debouncedSearch,
    page,
    setPage,
    filters,
    setFilter,
    resetFilters,
    visibleColumns,
    toggleColumn,
    deleteEntity,
    setDeleteEntity,
    notice,
    setNotice,
  } = useOrdersPage();

  const { data, isLoading, isError } = useOrdersQuery({
    search: debouncedSearch,
    page,
    perPage,
    dateFrom: filters.dateFrom || undefined,
    dateTo: filters.dateTo || undefined,
    printIds: filters.printIds.length > 0 ? filters.printIds : undefined,
    materialId: toOptionalNumber(filters.materialId),
    optionId: toOptionalNumber(filters.optionId),
    docs: toOptionalNumber(filters.docs),
    managerId: toOptionalNumber(filters.managerId),
    designerId: toOptionalNumber(filters.designerId),
    statusTypes: filters.statusTypes.length > 0 ? filters.statusTypes : undefined,
    storageType: toOptionalNumber(filters.storageType),
    serviceType: toOptionalNumber(filters.serviceType),
    archived: filters.archived || undefined,
    deleted: filters.deleted || undefined,
  });

  const printingSelect = usePrintingSelectQuery();
  const materialSelect = useMaterialSelectQuery();
  const materialOptionSelect = useMaterialOptionSelectQuery(filters.materialId || 0, {
    enabled: Boolean(filters.materialId),
  });
  const clientDocsTypes = useClientDocsTypesQuery();
  const managerSelect = useUserSelectQuery();
  const designerSelect = useUserSelectQuery();
  const orderStatusTypes = useOrderStatusTypesQuery();
  const orderStorageTypes = useOrderStorageTypesQuery();
  const orderServiceTypes = useOrderServiceTypesQuery();

  return (
    <div className="h-full flex flex-col p-6 w-full">
      {notice && (
        <AlertBanner variant="success" className="mb-4">
          {notice}
        </AlertBanner>
      )}

      <OrdersHeader
        search={search}
        onSearchChange={setSearch}
        onAdd={() => setNotice('Форма создания заказа будет следующим этапом.')}
      />

      <OrdersFilters
        values={filters}
        printingOptions={printingSelect.data ?? []}
        materialOptions={materialSelect.data ?? []}
        materialOptionOptions={materialOptionSelect.data ?? []}
        docsOptions={clientDocsTypes.data ?? []}
        managerOptions={managerSelect.data ?? []}
        designerOptions={designerSelect.data ?? []}
        statusOptions={orderStatusTypes.data ?? []}
        storageOptions={orderStorageTypes.data ?? []}
        serviceOptions={orderServiceTypes.data ?? []}
        visibleColumns={visibleColumns}
        onChange={setFilter}
        onToggleColumn={toggleColumn}
        onReset={resetFilters}
      />

      <OrdersTable
        orders={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        visibleColumns={visibleColumns}
        onPageChange={setPage}
      />

      <DeleteOrderModal
        open={deleteEntity != null}
        order={deleteEntity}
        onClose={() => setDeleteEntity(null)}
        onSuccess={() => setNotice('Заказ удалён')}
      />
    </div>
  );
};
