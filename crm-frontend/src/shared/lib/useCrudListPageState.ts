import { useEffect, useState } from 'react';

type CrudEntity = { id: number };

interface UseCrudListPageStateOptions {
  searchDelay?: number;
}

export const useCrudListPageState = <TEntity extends CrudEntity>({
  searchDelay = 300,
}: UseCrudListPageStateOptions = {}) => {
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [page, setPage] = useState(1);
  const [createOpen, setCreateOpen] = useState(false);
  const [editId, setEditId] = useState<number | null>(null);
  const [deleteEntity, setDeleteEntity] = useState<TEntity | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  useEffect(() => {
    const timer = window.setTimeout(() => {
      setDebouncedSearch(search);
      setPage(1);
    }, searchDelay);

    return () => window.clearTimeout(timer);
  }, [search, searchDelay]);

  useEffect(() => {
    if (!notice) return undefined;

    const timer = window.setTimeout(() => setNotice(null), 4000);
    return () => window.clearTimeout(timer);
  }, [notice]);

  const openCreate = () => {
    setEditId(null);
    setCreateOpen(true);
  };

  const startEdit = (entity: TEntity) => {
    setCreateOpen(false);
    setEditId(entity.id);
  };

  const closeForm = () => {
    setCreateOpen(false);
    setEditId(null);
  };

  return {
    search,
    setSearch,
    debouncedSearch,
    page,
    setPage,
    createOpen,
    editId,
    deleteEntity,
    setDeleteEntity,
    notice,
    setNotice,
    formOpen: createOpen || editId != null,
    openCreate,
    startEdit,
    closeForm,
  };
};
