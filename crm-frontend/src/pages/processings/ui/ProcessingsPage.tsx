import { useProcessingsQuery } from '@/entities/processing';
import { AlertBanner } from '@/shared/ui';
import {
  ProcessingsHeader,
  ProcessingsTable,
  ProcessingFormModal,
  DeleteProcessingModal,
} from '@/features/processings';
import { useProcessingsPage } from '../model/useProcessingsPage';

export const ProcessingsPage = () => {
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
  } = useProcessingsPage();

  const { data, isLoading, isError } = useProcessingsQuery({
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

      <ProcessingsHeader
        search={search}
        onSearchChange={setSearch}
        onAdd={openCreate}
      />

      <ProcessingsTable
        processings={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={startEdit}
        onDelete={setDeleteEntity}
      />

      <ProcessingFormModal
        open={formOpen}
        mode={editId != null ? 'edit' : 'create'}
        processingId={editId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Обработка сохранена' : 'Обработка создана')
        }
      />

      <DeleteProcessingModal
        open={deleteEntity != null}
        processing={deleteEntity}
        onClose={() => setDeleteEntity(null)}
        onSuccess={() => setNotice('Обработка удалена')}
      />
    </div>
  );
};
