import { useState, useEffect } from 'react';
import { useMaterialsQuery } from '@/entities/material';
import type { Material } from '@/entities/material';
import { AlertBanner } from '@/shared/ui';
import {
  MaterialsHeader,
  MaterialsTable,
  MaterialFormModal,
  DeleteMaterialModal,
} from '@/features/materials';

export const MaterialsPage = () => {
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [page, setPage] = useState(1);
  const perPage = 20;

  const [createOpen, setCreateOpen] = useState(false);
  const [editMaterialId, setEditMaterialId] = useState<number | null>(null);
  const [deleteMaterial, setDeleteMaterial] = useState<Material | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  const { data, isLoading, isError } = useMaterialsQuery({
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

  const formOpen = createOpen || editMaterialId != null;

  const closeForm = () => {
    setCreateOpen(false);
    setEditMaterialId(null);
  };

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
        onAdd={() => {
          setEditMaterialId(null);
          setCreateOpen(true);
        }}
      />

      <MaterialsTable
        materials={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={(material) => {
          setCreateOpen(false);
          setEditMaterialId(material.id);
        }}
        onDelete={setDeleteMaterial}
      />

      <MaterialFormModal
        open={formOpen}
        mode={editMaterialId != null ? 'edit' : 'create'}
        materialId={editMaterialId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Материал сохранён' : 'Материал создан')
        }
      />

      <DeleteMaterialModal
        open={deleteMaterial != null}
        material={deleteMaterial}
        onClose={() => setDeleteMaterial(null)}
        onSuccess={() => setNotice('Материал удалён')}
      />
    </div>
  );
};
