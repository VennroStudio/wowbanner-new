import { useClientsQuery } from '@/entities/client';
import { AlertBanner } from '@/shared/ui';
import {
  ClientsHeader,
  ClientsTable,
  ClientFormModal,
  DeleteClientModal,
} from '@/features/clients';
import { useClientsPage } from '../model/useClientsPage';

export const ClientsPage = () => {
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
  } = useClientsPage();

  const { data, isLoading, isError } = useClientsQuery({
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

      <ClientsHeader
        search={search}
        onSearchChange={setSearch}
        onAddClient={openCreate}
      />

      <ClientsTable
        clients={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={startEdit}
        onDelete={setDeleteEntity}
      />

      <ClientFormModal
        open={formOpen}
        mode={editId != null ? 'edit' : 'create'}
        clientId={editId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Клиент сохранён' : 'Клиент создан')
        }
      />

      <DeleteClientModal
        open={deleteEntity != null}
        client={deleteEntity}
        onClose={() => setDeleteEntity(null)}
        onSuccess={() => setNotice('Клиент удалён')}
      />
    </div>
  );
};
