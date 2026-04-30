import { usePrintingsQuery } from '@/entities/printing';
import { AlertBanner } from '@/shared/ui';
import {
  PrintingsHeader,
  PrintingsTable,
  PrintingFormModal,
  DeletePrintingModal,
} from '@/features/printings';
import { usePrintingsPage } from '../model/usePrintingsPage';

export const PrintingsPage = () => {
  const perPage = 20;
  const {
    search,
    setSearch,
    debouncedSearch,
    page,
    setPage,
    editId,
    deleteEntity,
    setDeleteEntity,
    notice,
    setNotice,
    formOpen,
    openCreate,
    startEdit,
    closeForm,
  } = usePrintingsPage();

  const { data, isLoading, isError } = usePrintingsQuery({
    search: debouncedSearch,
    page,
    perPage,
  });

  return (
    <div className="h-full flex flex-col p-6 w-full">
      {notice && (
        <AlertBanner variant="success" className="mb-4">
          {notice}
        </AlertBanner>
      )}

      <PrintingsHeader
        search={search}
        onSearchChange={setSearch}
        onAdd={openCreate}
      />

      <PrintingsTable
        printings={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={startEdit}
        onDelete={setDeleteEntity}
      />

      <PrintingFormModal
        open={formOpen}
        mode={editId != null ? 'edit' : 'create'}
        printingId={editId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Тип печати сохранён' : 'Тип печати создан')
        }
      />

      <DeletePrintingModal
        open={deleteEntity != null}
        printing={deleteEntity}
        onClose={() => setDeleteEntity(null)}
        onSuccess={() => setNotice('Тип печати удалён')}
      />
    </div>
  );
};
