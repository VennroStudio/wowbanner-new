import type { UseFormRegister, UseFormGetValues } from 'react-hook-form';
import { CLIENT_TYPE_OPTIONS, DOCS_OPTIONS } from '@/shared/constants/clientDictionaries';
import type { ClientFormValues } from '../lib/clientFormSchema';
import { fieldSelectClass } from '../lib/formFieldClasses';

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
        >
          {CLIENT_TYPE_OPTIONS.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
      </div>
      <div>
        <label className="block text-xs font-medium text-slate-500 mb-1">Документы *</label>
        <select {...register('docs', { valueAsNumber: true })} className={fieldSelectClass}>
          {DOCS_OPTIONS.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
      </div>
    </div>
  );
};
