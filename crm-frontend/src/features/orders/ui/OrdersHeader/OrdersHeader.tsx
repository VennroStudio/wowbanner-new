import { Plus } from 'lucide-react';
import { SearchField } from '@/shared/ui';

interface OrdersHeaderProps {
  search: string;
  onSearchChange: (value: string) => void;
  onAdd: () => void;
}

export const OrdersHeader = ({ search, onSearchChange, onAdd }: OrdersHeaderProps) => (
  <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
    <h1 className="text-xl font-semibold text-slate-900 tracking-tight shrink-0">Заказы</h1>

    <div className="flex flex-wrap items-center gap-3">
      <div className="w-full min-w-[220px] sm:w-80">
        <SearchField value={search} onChange={onSearchChange} placeholder="Поиск по клиенту…" />
      </div>

      <button
        type="button"
        onClick={onAdd}
        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
          text-white text-sm font-medium rounded-lg transition-colors cursor-pointer"
      >
        <Plus size={15} strokeWidth={2.5} />
        Создать заказ
      </button>
    </div>
  </div>
);
