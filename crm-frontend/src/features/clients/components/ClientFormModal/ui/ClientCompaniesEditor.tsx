import { Plus, Trash2 } from 'lucide-react';
import type { FieldArrayWithId, UseFormRegister, FieldErrors } from 'react-hook-form';
import type { ClientFormValues } from '../lib/clientFormSchema';

interface Props {
  companyFields: FieldArrayWithId<ClientFormValues, 'companies', 'fieldId'>[];
  register: UseFormRegister<ClientFormValues>;
  appendCompany: (v: { name: string }) => void;
  removeCompany: (index: number) => void;
  errors: FieldErrors<ClientFormValues>;
}

export const ClientCompaniesEditor = ({
  companyFields,
  register,
  appendCompany,
  removeCompany,
  errors,
}: Props) => (
  <div>
    <div className="flex items-center justify-between mb-2">
      <span className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Компании</span>
      <button
        type="button"
        onClick={() => appendCompany({ name: '' })}
        className="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700"
      >
        <Plus size={14} /> Добавить
      </button>
    </div>
    {errors.companies?.[0]?.name && (
      <p className="text-xs text-red-600 mb-2">{errors.companies[0].name.message}</p>
    )}
    <div className="space-y-2">
      {companyFields.map((field, index) => (
        <div key={field.fieldId} className="flex gap-2 items-center">
          <input
            {...register(`companies.${index}.name`)}
            placeholder="Название компании"
            className="flex-1 px-3 py-1.5 text-sm border border-slate-200 rounded-lg"
          />
          <button
            type="button"
            onClick={() => removeCompany(index)}
            className="p-1.5 text-slate-400 hover:text-red-600 rounded-lg"
            aria-label="Удалить компанию"
          >
            <Trash2 size={16} />
          </button>
        </div>
      ))}
    </div>
  </div>
);
