import { Plus } from 'lucide-react';
import { SearchField } from '@/shared/ui';

interface ProcessingsHeaderProps {
  onAdd?: () => void;
  search: string;
  onSearchChange: (value: string) => void;
}

export const ProcessingsHeader = ({ onAdd, search, onSearchChange }: ProcessingsHeaderProps) => (
  <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
    <h1 className="text-xl font-semibold text-slate-900 tracking-tight shrink-0">Доп. обработки</h1>

    <div className="flex flex-wrap items-center gap-3">
      <div className="w-full min-w-[200px] sm:w-72">
        <SearchField value={search} onChange={onSearchChange} placeholder="Поиск по названию…" />
      </div>

      <button
        type="button"
        onClick={onAdd}
        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
          text-white text-sm font-medium rounded-lg transition-colors cursor-pointer"
      >
        <Plus size={15} strokeWidth={2.5} />
        Создать обработку
      </button>
    </div>
  </div>
);
