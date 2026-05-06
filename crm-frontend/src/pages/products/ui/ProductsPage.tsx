import { useProductsQuery } from '@/entities/product';
import { AlertBanner } from '@/shared/ui';
import {
  ProductsHeader,
  ProductsTable,
  ProductFormModal,
  DeleteProductModal,
} from '@/features/products';
import { useProductsPage } from '../model/useProductsPage';

export const ProductsPage = () => {
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
  } = useProductsPage();

  const { data, isLoading, isError } = useProductsQuery({
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

      <ProductsHeader
        search={search}
        onSearchChange={setSearch}
        onAdd={openCreate}
      />

      <ProductsTable
        products={data?.data?.items}
        total={data?.data?.count}
        isLoading={isLoading}
        isError={isError}
        page={page}
        perPage={perPage}
        onPageChange={setPage}
        onEdit={startEdit}
        onDelete={setDeleteEntity}
      />

      <ProductFormModal
        open={formOpen}
        mode={editId != null ? 'edit' : 'create'}
        productId={editId ?? undefined}
        onClose={closeForm}
        onSuccess={(mode) =>
          setNotice(mode === 'edit' ? 'Продукт сохранён' : 'Продукт создан')
        }
      />

      <DeleteProductModal
        open={deleteEntity != null}
        product={deleteEntity}
        onClose={() => setDeleteEntity(null)}
        onSuccess={() => setNotice('Продукт удалён')}
      />
    </div>
  );
};
