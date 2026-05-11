import { useMemo, useState } from 'react';
import type { Client } from '@/entities/client';
import { useClientsQuery } from '@/entities/client';
import { ModalDialog, SearchField } from '@/shared/ui';
import { getClientDisplayName } from '../lib/clientPresentation';

interface ClientSelectOption {
  id: number;
  name: string;
  email: string | null;
  phone: string | null;
  docs: string | null;
}

interface OrderClientSelectModalProps {
  open: boolean;
  selectedClientId?: number | null;
  onClose: () => void;
  onSelect: (client: ClientSelectOption) => void;
}

const mapClient = (client: Client): ClientSelectOption => ({
  id: client.id,
  name: getClientDisplayName(client),
  email: client.email,
  phone: client.phones[0]?.phone ?? null,
  docs: client.docs?.label ?? null,
});

export const OrderClientSelectModal = ({
  open,
  selectedClientId,
  onClose,
  onSelect,
}: OrderClientSelectModalProps) => {
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);

  const { data, isLoading } = useClientsQuery({
    page,
    perPage: 20,
    search,
  });

  const clients = useMemo(() => (data?.data?.items ?? []).map(mapClient), [data?.data?.items]);
  const count = data?.data?.count ?? 0;
  const canLoadMore = page * 20 < count;

  return (
    <ModalDialog
      open={open}
      title="Выберите клиента"
      titleId="order-client-select-title"
      onClose={onClose}
      size="4xl"
    >
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden">
        <div className="space-y-4 px-5 py-4">
          <div className="grid grid-cols-1 gap-3 md:grid-cols-[160px_1fr]">
            <div className="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-500">
              Страница {page}
            </div>
            <SearchField
              value={search}
              onChange={(value) => {
                setSearch(value);
                setPage(1);
              }}
              placeholder="Поиск по ФИО, email или телефону…"
            />
          </div>
        </div>

        <div className="min-h-0 flex-1 overflow-y-auto px-5 pb-4">
          <div className="overflow-hidden rounded-xl border border-slate-200">
            <table className="min-w-full table-fixed">
              <thead className="bg-slate-800 text-white">
                <tr className="text-left text-xs uppercase tracking-wide">
                  <th className="px-4 py-3 w-20">№</th>
                  <th className="px-4 py-3">ФИО</th>
                  <th className="px-4 py-3">email</th>
                  <th className="px-4 py-3">телефон</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 bg-white">
                {isLoading ? (
                  <tr>
                    <td colSpan={4} className="px-4 py-8 text-center text-sm text-slate-500">
                      Загрузка…
                    </td>
                  </tr>
                ) : clients.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="px-4 py-8 text-center text-sm text-slate-500">
                      Клиенты не найдены.
                    </td>
                  </tr>
                ) : (
                  clients.map((client) => {
                    const isSelected = client.id === selectedClientId;
                    return (
                      <tr
                        key={client.id}
                        className={`cursor-pointer transition-colors hover:bg-blue-50 ${
                          isSelected ? 'bg-blue-50' : ''
                        }`}
                        onClick={() => onSelect(client)}
                      >
                        <td className="px-4 py-3 text-sm font-medium text-slate-700">{client.id}</td>
                        <td className="px-4 py-3 text-sm text-slate-800">{client.name}</td>
                        <td className="px-4 py-3 text-sm text-slate-600">{client.email || '—'}</td>
                        <td className="px-4 py-3 text-sm text-slate-600">{client.phone || '—'}</td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>

          <div className="mt-4 flex items-center justify-between gap-3">
            <p className="text-sm text-slate-500">Всего найдено: {count}</p>
            {canLoadMore ? (
              <button
                type="button"
                onClick={() => setPage((current) => current + 1)}
                className="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 cursor-pointer"
              >
                Показать ещё
              </button>
            ) : null}
          </div>
        </div>
      </div>
    </ModalDialog>
  );
};
