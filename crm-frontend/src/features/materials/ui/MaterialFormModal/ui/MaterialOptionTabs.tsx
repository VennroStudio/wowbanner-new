import { Plus, X } from 'lucide-react';

interface MaterialOptionTabsProps {
  optionLabels: string[];
  activeTab: 'base' | number;
  onSelect: (tab: 'base' | number) => void;
  onAdd: () => void;
  onRemove: (index: number) => void;
  disabled?: boolean;
}

export const MaterialOptionTabs = ({
  optionLabels,
  activeTab,
  onSelect,
  onAdd,
  onRemove,
  disabled = false,
}: MaterialOptionTabsProps) => {
  return (
    <div className="border-b border-slate-200">
      <div className="flex flex-wrap gap-2 px-5 pt-4">
        <button
          type="button"
          onClick={() => onSelect('base')}
          className={`rounded-t-xl border px-3 py-2 text-sm font-medium transition ${
            activeTab === 'base'
              ? 'border-slate-200 border-b-white bg-white text-slate-900'
              : 'border-transparent bg-slate-100 text-slate-500 hover:bg-slate-200'
          }`}
        >
          Основные данные
        </button>

        {optionLabels.map((label, index) => (
          <div
            key={`${label}-${index}`}
            className={`flex items-center rounded-t-xl border px-3 py-2 text-sm transition ${
              activeTab === index
                ? 'border-slate-200 border-b-white bg-white text-slate-900'
                : 'border-transparent bg-slate-100 text-slate-500'
            }`}
          >
            <button
              type="button"
              onClick={() => onSelect(index)}
              className="font-medium"
            >
              {label || `Опция ${index + 1}`}
            </button>
            <button
              type="button"
              onClick={() => onRemove(index)}
              className="ml-2 rounded p-0.5 text-slate-400 hover:text-red-600"
              aria-label={`Удалить опцию ${index + 1}`}
              disabled={disabled}
            >
              <X size={14} />
            </button>
          </div>
        ))}

        <button
          type="button"
          onClick={onAdd}
          className="inline-flex items-center gap-1 rounded-t-xl border border-dashed border-slate-300 px-3 py-2 text-sm font-medium text-blue-600 hover:border-blue-300 hover:bg-blue-50"
          disabled={disabled}
        >
          <Plus size={14} />
          Добавить опцию
        </button>
      </div>
    </div>
  );
};
