import { useState, useEffect } from 'react';
import { usePrintingsQuery } from '@/entities/printing';
import type { Printing } from '@/entities/printing';
import { AlertBanner } from '@/shared/ui';
import {
  PrintingsHeader,
  PrintingsTable,
  PrintingFormModal,
  DeletePrintingModal,
} from '@/features/printings';

export const PrintingsPage = () => {
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [page, setPage] = useState(1);
  const perPage = 20;

  const [createOpen, setCreateOpen] = useState(false);
  const [editPrintingId, setEditPrintingId] = useState<number | null>(null);
  const [deletePrinting, setDeletePrinting] = useState<Printing | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  const { data, isLoading, isError } = usePrintingsQuery({
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

  useEffect(() => {
    if (!notice) return;
    const t = window.setTimeout(() => setNotice(null), 4000);
    return () => clearTimeout(t);
  }, [notice]);

  const formOpen = createOpen || editPrintingId != null;

  const closeForm = () => {
    setCreateOpen(false);
    setEditPrintingId(null);
  };

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
        onAdd={() => {
          setEditPrintingId(null);
          setCreateOpen(true);
        }}
      />

      <PrintingsTable
        printings={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={(p) => {
          setCreateOpen(false);
          setEditPrintingId(p.id);
        }}
        onDelete={setDeletePrinting}
      />

      <PrintingFormModal
        open={formOpen}
        mode={editPrintingId != null ? 'edit' : 'create'}
        printingId={editPrintingId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Тип печати сохранён' : 'Тип печати создан')
        }
      />

      <DeletePrintingModal
        open={deletePrinting != null}
        printing={deletePrinting}
        onClose={() => setDeletePrinting(null)}
        onSuccess={() => setNotice('Тип печати удалён')}
      />
    </div>
  );
};
