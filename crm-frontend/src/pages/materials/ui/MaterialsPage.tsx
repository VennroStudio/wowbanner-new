import { useMaterialsQuery } from '@/entities/material';
import { AlertBanner } from '@/shared/ui';
import {
  MaterialsHeader,
  MaterialsTable,
  MaterialFormModal,
  DeleteMaterialModal,
} from '@/features/materials';
import { useMaterialsPage } from '../model/useMaterialsPage';

export const MaterialsPage = () => {
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
  } = useMaterialsPage();

  const { data, isLoading, isError } = useMaterialsQuery({
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

      <MaterialsHeader
        search={search}
        onSearchChange={setSearch}
        onAdd={openCreate}
      />

      <MaterialsTable
        materials={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={startEdit}
        onDelete={setDeleteEntity}
      />

      <MaterialFormModal
        open={formOpen}
        mode={editId != null ? 'edit' : 'create'}
        materialId={editId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Материал сохранён' : 'Материал создан')
        }
      />

      <DeleteMaterialModal
        open={deleteEntity != null}
        material={deleteEntity}
        onClose={() => setDeleteEntity(null)}
        onSuccess={() => setNotice('Материал удалён')}
      />
    </div>
  );
};
