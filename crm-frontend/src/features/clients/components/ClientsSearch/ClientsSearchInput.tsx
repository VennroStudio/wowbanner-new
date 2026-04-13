import { Search } from 'lucide-react';

interface ClientsSearchInputProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  /** Tailwind width и др., по умолчанию полная ширина родителя */
  inputClassName?: string;
}

const defaultPlaceholder = 'Поиск по имени, email, телефону...';

export const ClientsSearchInput = ({
  value,
  onChange,
  placeholder = defaultPlaceholder,
  inputClassName = 'w-full',
}: ClientsSearchInputProps) => (
  <div className="relative">
    <Search
      size={14}
      strokeWidth={2}
      className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
      aria-hidden
    />
    <input
      type="search"
      value={value}
      onChange={(e) => onChange(e.target.value)}
      placeholder={placeholder}
      autoComplete="off"
      className={`${inputClassName} pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-lg
        text-slate-700 placeholder-slate-400 outline-none
        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10
        hover:border-slate-300 transition-colors`}
    />
  </div>
);
