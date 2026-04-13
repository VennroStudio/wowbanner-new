import { Plus, Download } from 'lucide-react';
import { SearchField } from '@/shared/ui';

interface ClientsHeaderProps {
  onAddClient?: () => void;
  onExport?: () => void;
  search: string;
  onSearchChange: (value: string) => void;
}

export const ClientsHeader = ({
  onAddClient,
  onExport,
  search,
  onSearchChange,
}: ClientsHeaderProps) => (
  <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
    <h1 className="text-xl font-semibold text-slate-900 tracking-tight shrink-0">Клиенты</h1>

    <div className="flex flex-wrap items-center gap-3">
      <div className="w-full min-w-[200px] sm:w-72">
        <SearchField value={search} onChange={onSearchChange} />
      </div>

      <button
        type="button"
        onClick={onAddClient}
        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
          text-white text-sm font-medium rounded-lg transition-colors cursor-pointer"
      >
        <Plus size={15} strokeWidth={2.5} />
        Создать клиента
      </button>

      <button
        type="button"
        onClick={onExport}
        className="flex items-center gap-2 px-3 py-2 bg-white border border-slate-200
          hover:bg-slate-50 hover:border-slate-300 text-slate-600 text-sm
          font-medium rounded-lg transition-colors cursor-pointer"
      >
        <Download size={14} />
        Экспорт CSV
      </button>
    </div>
  </div>
);
