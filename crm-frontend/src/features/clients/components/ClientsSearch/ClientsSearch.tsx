import { ClientsSearchInput } from './ClientsSearchInput';

interface ClientsSearchProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
}

/** Вариант поиска в отдельной карточке (если понадобится вне шапки) */
export const ClientsSearch = ({ value, onChange, placeholder }: ClientsSearchProps) => (
  <div className="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row gap-4">
    <ClientsSearchInput value={value} onChange={onChange} placeholder={placeholder} />
  </div>
);
