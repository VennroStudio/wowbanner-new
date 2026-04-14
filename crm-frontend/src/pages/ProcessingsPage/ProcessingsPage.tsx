import { useState, useEffect } from 'react';
import { useProcessingsQuery } from '@/entities/processing';
import type { Processing } from '@/entities/processing';
import { AlertBanner } from '@/shared/ui';
import {
  ProcessingsHeader,
  ProcessingsTable,
  ProcessingFormModal,
  DeleteProcessingModal,
} from '@/features/processings';

export const ProcessingsPage = () => {
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [page, setPage] = useState(1);
  const perPage = 20;

  const [createOpen, setCreateOpen] = useState(false);
  const [editProcessingId, setEditProcessingId] = useState<number | null>(null);
  const [deleteProcessing, setDeleteProcessing] = useState<Processing | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  const { data, isLoading, isError } = useProcessingsQuery({
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

  const formOpen = createOpen || editProcessingId != null;

  const closeForm = () => {
    setCreateOpen(false);
    setEditProcessingId(null);
  };

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
        onAdd={() => {
          setEditProcessingId(null);
          setCreateOpen(true);
        }}
      />

      <ProcessingsTable
        processings={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={(p) => {
          setCreateOpen(false);
          setEditProcessingId(p.id);
        }}
        onDelete={setDeleteProcessing}
      />

      <ProcessingFormModal
        open={formOpen}
        mode={editProcessingId != null ? 'edit' : 'create'}
        processingId={editProcessingId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Обработка сохранена' : 'Обработка создана')
        }
      />

      <DeleteProcessingModal
        open={deleteProcessing != null}
        processing={deleteProcessing}
        onClose={() => setDeleteProcessing(null)}
        onSuccess={() => setNotice('Обработка удалена')}
      />
    </div>
  );
};
