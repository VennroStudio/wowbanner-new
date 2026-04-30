import { Plus } from 'lucide-react';
import { SearchField } from '@/shared/ui';

interface ClientsHeaderProps {
  onAddClient?: () => void;
  search: string;
  onSearchChange: (value: string) => void;
}

export const ClientsHeader = ({
  onAddClient,
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
    </div>
  </div>
);
