import type { UseFormRegister, UseFormGetValues } from 'react-hook-form';
import {
  useClientDocsTypesQuery,
  useClientTypesQuery,
} from '@/entities/client';
import type { ClientFormValues } from '../lib/clientFormSchema';
import { fieldSelectClass } from '@/shared/ui';

interface Props {
  register: UseFormRegister<ClientFormValues>;
  getValues: UseFormGetValues<ClientFormValues>;
  appendCompany: (v: { name: string }) => void;
  replaceCompanies: (v: { id?: number; name: string }[]) => void;
}

export const ClientFormTypeDocsFields = ({
  register,
  getValues,
  appendCompany,
  replaceCompanies,
}: Props) => {
  const { data: clientTypes = [], isLoading: isTypesLoading } = useClientTypesQuery();
  const { data: docsTypes = [], isLoading: isDocsLoading } = useClientDocsTypesQuery();
  const { onChange: typeOnChange, ...typeRest } = register('type', { valueAsNumber: true });

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div>
        <label className="block text-xs font-medium text-slate-500 mb-1">Тип клиента *</label>
        <select
          {...typeRest}
          onChange={(e) => {
            void typeOnChange(e);
            const v = Number(e.target.value);
            if (v === 2 && getValues('companies').length === 0) {
              appendCompany({ name: '' });
            }
            if (v === 1) {
              replaceCompanies([]);
            }
          }}
          className={fieldSelectClass}
          disabled={isTypesLoading}
        >
          {clientTypes.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
      </div>
      <div>
        <label className="block text-xs font-medium text-slate-500 mb-1">Документы *</label>
        <select
          {...register('docs', { valueAsNumber: true })}
          className={fieldSelectClass}
          disabled={isDocsLoading}
        >
          {docsTypes.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
      </div>
    </div>
  );
};
