import React, { useState, useEffect } from 'react';
import { useClientsQuery } from '@/entities/client';
import { ClientsHeader, ClientsTable } from '@/features/clients';

export const ClientsPage = () => {
    const [search, setSearch] = useState('');
    const [debouncedSearch, setDebouncedSearch] = useState('');
    const [page, setPage] = useState(1);
    const perPage = 20;

    const { data, isLoading, isError } = useClientsQuery({
        search: debouncedSearch,
        page,
        perPage,
    });

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedSearch(search);
            setPage(1);
        }, 300);
        return () => clearTimeout(timer);
    }, [search]);

    return (
        <div className="h-full flex flex-col p-6 w-full">
            <ClientsHeader
                search={search}
                onSearchChange={setSearch}
                onAddClient={() => console.log('add client')}
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
                onEdit={(client) => console.log('edit', client.id)}
                onDelete={(client) => console.log('delete', client.id)}
            />
        </div>
    );
};