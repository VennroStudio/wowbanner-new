import React from 'react';
import { Search as SearchIcon } from 'lucide-react';

interface ClientsSearchProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
}

export const ClientsSearch: React.FC<ClientsSearchProps> = ({ value, onChange, placeholder }) => {
  return (
    <div className="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row gap-4">
      <div className="relative flex-1">
        <SearchIcon size={18} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          type="text"
          placeholder={placeholder || "Поиск по имени, телефону или email..."}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          className="w-full pl-10 pr-4 py-2 bg-slate-50 hover:bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-blue-100 focus:bg-white transition-colors outline-none"
        />
      </div>
    </div>
  );
};
