import type { Product } from '@/entities/product';
import { PaginationBar } from '@/shared/ui';
import { ProductTableRow } from './ProductTableRow';
import { ProductsTableSkeleton } from './ProductsTableSkeleton';
import { ProductsTableEmpty } from './ProductsTableEmpty';
import { ProductsTableError } from './ProductsTableError';

interface Props {
  products: Product[] | undefined;
  total: number | undefined;
  isLoading: boolean;
  isError: boolean;
  page: number;
  perPage: number;
  onPageChange: (page: number) => void;
  onEdit?: (product: Product) => void;
  onDelete?: (product: Product) => void;
}

export const ProductsTable = ({
  products,
  total,
  isLoading,
  isError,
  page,
  perPage,
  onPageChange,
  onEdit,
  onDelete,
}: Props) => {
  const totalPages = total ? Math.ceil(total / perPage) : 1;
  const hasData = !isLoading && !isError && products && products.length > 0;

  return (
    <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[400px]">
      <div className="overflow-x-auto flex-1">
        <table className="w-full text-left border-collapse table-fixed">
          <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              <th className="px-5 py-3 w-[80px] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                ID
              </th>
              <th className="px-5 py-3 w-[20%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Название
              </th>
              <th className="px-5 py-3 w-[32%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Материалы
              </th>
              <th className="px-5 py-3 w-[24%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Типы печати
              </th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <ProductsTableSkeleton />
            ) : isError ? (
              <ProductsTableError />
            ) : !products || products.length === 0 ? (
              <ProductsTableEmpty />
            ) : (
              products.map((product) => (
                <ProductTableRow
                  key={product.id}
                  product={product}
                  onEdit={onEdit}
                  onDelete={onDelete}
                />
              ))
            )}
          </tbody>
        </table>
      </div>

      {hasData && (
        <PaginationBar
          page={page}
          totalPages={totalPages}
          total={total ?? 0}
          onPageChange={onPageChange}
        />
      )}
    </div>
  );
};
