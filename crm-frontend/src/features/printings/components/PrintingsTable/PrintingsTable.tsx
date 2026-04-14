import type { Printing } from '@/entities/printing';
import { PaginationBar } from '@/shared/ui';
import { PrintingTableRow } from './PrintingTableRow';
import { PrintingsTableSkeleton } from './PrintingsTableSkeleton';
import { PrintingsTableEmpty } from './PrintingsTableEmpty';
import { PrintingsTableError } from './PrintingsTableError';

interface Props {
  printings: Printing[] | undefined;
  total: number | undefined;
  isLoading: boolean;
  isError: boolean;
  page: number;
  perPage: number;
  onPageChange: (page: number) => void;
  onEdit?: (printing: Printing) => void;
  onDelete?: (printing: Printing) => void;
}

export const PrintingsTable = ({
  printings,
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
  const hasData = !isLoading && !isError && printings && printings.length > 0;

  return (
    <div className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col flex-1 min-h-[400px]">
      <div className="overflow-x-auto flex-1">
        <table className="w-full text-left border-collapse table-fixed">
          <thead>
            <tr className="bg-slate-50 border-b border-slate-200">
              <th className="px-5 py-3 w-[80px] text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                ID
              </th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Название
              </th>
              <th className="px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <PrintingsTableSkeleton />
            ) : isError ? (
              <PrintingsTableError />
            ) : !printings || printings.length === 0 ? (
              <PrintingsTableEmpty />
            ) : (
              printings.map((p) => (
                <PrintingTableRow key={p.id} printing={p} onEdit={onEdit} onDelete={onDelete} />
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
