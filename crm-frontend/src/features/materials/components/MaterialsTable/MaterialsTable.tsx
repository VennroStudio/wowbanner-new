import type { Material } from '@/entities/material';
import { PaginationBar } from '@/shared/ui';
import { MaterialTableRow } from './MaterialTableRow';
import { MaterialsTableSkeleton } from './MaterialsTableSkeleton';
import { MaterialsTableEmpty } from './MaterialsTableEmpty';
import { MaterialsTableError } from './MaterialsTableError';

interface Props {
  materials: Material[] | undefined;
  total: number | undefined;
  isLoading: boolean;
  isError: boolean;
  page: number;
  perPage: number;
  onPageChange: (page: number) => void;
  onEdit?: (material: Material) => void;
  onDelete?: (material: Material) => void;
}

export const MaterialsTable = ({
  materials,
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
  const hasData = !isLoading && !isError && materials && materials.length > 0;

  return (
    <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[400px]">
      <div className="overflow-x-auto flex-1">
        <table className="w-full text-left border-collapse table-fixed">
          <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              <th className="px-5 py-3 w-[72px] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                ID
              </th>
              <th className="px-5 py-3 w-[22%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Название
              </th>
              <th className="px-5 py-3 w-[32%] min-w-0 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Описание
              </th>
              <th className="px-5 py-3 w-[20%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Фото
              </th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <MaterialsTableSkeleton />
            ) : isError ? (
              <MaterialsTableError />
            ) : !materials || materials.length === 0 ? (
              <MaterialsTableEmpty />
            ) : (
              materials.map((m) => (
                <MaterialTableRow key={m.id} material={m} onEdit={onEdit} onDelete={onDelete} />
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
