import type { Processing } from '@/entities/processing';
import { PaginationBar } from '@/shared/ui';
import { ProcessingTableRow } from './ProcessingTableRow';
import { ProcessingsTableSkeleton } from './ProcessingsTableSkeleton';
import { ProcessingsTableEmpty } from './ProcessingsTableEmpty';
import { ProcessingsTableError } from './ProcessingsTableError';

interface Props {
  processings: Processing[] | undefined;
  total: number | undefined;
  isLoading: boolean;
  isError: boolean;
  page: number;
  perPage: number;
  onPageChange: (page: number) => void;
  onEdit?: (processing: Processing) => void;
  onDelete?: (processing: Processing) => void;
}

export const ProcessingsTable = ({
  processings,
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
  const hasData = !isLoading && !isError && processings && processings.length > 0;

  return (
    <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[400px]">
      <div className="overflow-x-auto flex-1">
        <table className="w-full text-left border-collapse table-fixed">
          <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              <th className="px-5 py-3 w-[64px] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                ID
              </th>
              <th className="px-5 py-3 w-[18%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Название
              </th>
              <th className="px-5 py-3 w-[20%] min-w-0 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Описание
              </th>
              <th className="px-5 py-3 w-[14%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Фото
              </th>
              <th className="px-5 py-3 w-[10%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Себестоимость
              </th>
              <th className="px-5 py-3 w-[10%] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Цена
              </th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <ProcessingsTableSkeleton />
            ) : isError ? (
              <ProcessingsTableError />
            ) : !processings || processings.length === 0 ? (
              <ProcessingsTableEmpty />
            ) : (
              processings.map((p) => (
                <ProcessingTableRow key={p.id} processing={p} onEdit={onEdit} onDelete={onDelete} />
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
