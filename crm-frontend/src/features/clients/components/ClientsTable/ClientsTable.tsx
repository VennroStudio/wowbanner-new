import React from 'react';
import type { Client } from '@/entities/client';
import { ClientTableRow } from './ClientTableRow';
import { ClientTableSkeleton } from './ClientTableSkeleton';
import { ClientsTableEmpty } from './ClientsTableEmpty';
import { ClientsTableError } from './ClientsTableError';
import { ClientsTablePagination } from './ClientsTablePagination';

interface Props {
  clients: Client[] | undefined;
  total: number | undefined;
  isLoading: boolean;
  isError: boolean;
  page: number;
  perPage: number;
  onPageChange: (page: number) => void;
  onEdit?: (client: Client) => void;
  onDelete?: (client: Client) => void;
}

export const ClientsTable: React.FC<Props> = ({
                                                clients,
                                                total,
                                                isLoading,
                                                isError,
                                                page,
                                                perPage,
                                                onPageChange,
                                                onEdit,
                                                onDelete,
                                              }) => {
  const totalPages = total ? Math.ceil(total / perPage) : 1;
  const hasData = !isLoading && !isError && clients && clients.length > 0;

  return (
      <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[400px]">
        <div className="overflow-x-auto flex-1">
          <table className="w-full text-left border-collapse table-fixed">
            <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              <th className="px-5 py-3 w-[80px] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">ID</th>
              <th className="px-5 py-3 w-[28%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Клиент</th>
              <th className="px-5 py-3 w-[22%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Компании</th>
              <th className="px-5 py-3 w-[26%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Контакты</th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Действия</th>
            </tr>
            </thead>
            <tbody>
            {isLoading ? (
                <ClientTableSkeleton />
            ) : isError ? (
                <ClientsTableError />
            ) : !clients || clients.length === 0 ? (
                <ClientsTableEmpty />
            ) : (
                clients.map((client) => (
                    <ClientTableRow
                        key={client.id}
                        client={client}
                        onEdit={onEdit}
                        onDelete={onDelete}
                    />
                ))
            )}
            </tbody>
          </table>
        </div>

        {hasData && (
            <ClientsTablePagination
                page={page}
                totalPages={totalPages}
                total={total ?? 0}
                onPageChange={onPageChange}
            />
        )}
      </div>
  );
};