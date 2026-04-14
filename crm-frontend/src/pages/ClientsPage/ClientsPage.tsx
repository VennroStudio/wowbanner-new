import { useState, useEffect } from 'react';
import { useClientsQuery } from '@/entities/client';
import type { Client } from '@/entities/client';
import { AlertBanner } from '@/shared/ui';
import {
  ClientsHeader,
  ClientsTable,
  ClientFormModal,
  DeleteClientModal,
} from '@/features/clients';

export const ClientsPage = () => {
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [page, setPage] = useState(1);
  const perPage = 20;

  const [createOpen, setCreateOpen] = useState(false);
  const [editClientId, setEditClientId] = useState<number | null>(null);
  const [deleteClient, setDeleteClient] = useState<Client | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  const { data, isLoading, isError } = useClientsQuery({
    search: debouncedSearch,
    page,
    perPage,
  });

  useEffect(() => {
    const timer = setTimeout(() => {
      setDebouncedSearch(search);
      setPage(1);
    }, 800);
    return () => clearTimeout(timer);
  }, [search]);

  useEffect(() => {
    if (!notice) return;
    const t = window.setTimeout(() => setNotice(null), 4000);
    return () => window.clearTimeout(t);
  }, [notice]);

  const formOpen = createOpen || editClientId != null;

  const closeForm = () => {
    setCreateOpen(false);
    setEditClientId(null);
  };

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
        onAddClient={() => {
          setEditClientId(null);
          setCreateOpen(true);
        }}
        onExport={() => console.log('export')}
      />

      <ClientsTable
        clients={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={(client) => {
          setCreateOpen(false);
          setEditClientId(client.id);
        }}
        onDelete={setDeleteClient}
      />

      <ClientFormModal
        open={formOpen}
        mode={editClientId != null ? 'edit' : 'create'}
        clientId={editClientId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Клиент сохранён' : 'Клиент создан')
        }
      />

      <DeleteClientModal
        open={deleteClient != null}
        client={deleteClient}
        onClose={() => setDeleteClient(null)}
        onSuccess={() => setNotice('Клиент удалён')}
      />
    </div>
  );
};
