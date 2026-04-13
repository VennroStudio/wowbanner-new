import { Plus, Trash2 } from 'lucide-react';
import { Controller, type Control, type FieldErrors, type UseFormRegister } from 'react-hook-form';
import type { FieldArrayWithId } from 'react-hook-form';
import { PHONE_TYPE_OPTIONS } from '@/shared/constants/clientDictionaries';
import { PhoneInputRu } from '@/shared/ui/PhoneInputRu';
import { fieldInputClass } from '@/shared/ui';
import type { ClientFormValues } from '../lib/clientFormSchema';

interface Props {
  phoneFields: FieldArrayWithId<ClientFormValues, 'phones', 'fieldId'>[];
  register: UseFormRegister<ClientFormValues>;
  control: Control<ClientFormValues>;
  errors: FieldErrors<ClientFormValues>;
  appendPhone: (v: { type: number; phone: string }) => void;
  removePhone: (index: number) => void;
}

export const ClientPhonesEditor = ({
  phoneFields,
  register,
  control,
  errors,
  appendPhone,
  removePhone,
}: Props) => (
  <div>
    <div className="flex items-center justify-between mb-2">
      <span className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Телефоны</span>
      <button
        type="button"
        onClick={() => appendPhone({ type: 1, phone: '' })}
        className="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700"
      >
        <Plus size={14} /> Добавить
      </button>
    </div>
    <div className="space-y-2">
      {phoneFields.map((field, index) => (
        <div key={field.fieldId} className="flex flex-wrap gap-2 items-start">
          <select
            {...register(`phones.${index}.type`, { valueAsNumber: true })}
            className="px-2 py-1.5 text-sm border border-slate-200 rounded-lg bg-white shrink-0"
          >
            {PHONE_TYPE_OPTIONS.map((o) => (
              <option key={o.value} value={o.value}>
                {o.label}
              </option>
            ))}
          </select>
          <Controller
            name={`phones.${index}.phone`}
            control={control}
            render={({ field }) => (
              <div className="flex-1 min-w-[140px]">
                <PhoneInputRu
                  ref={field.ref}
                  value={field.value}
                  onChange={field.onChange}
                  onBlur={field.onBlur}
                  name={field.name}
                  aria-invalid={errors.phones?.[index]?.phone ? true : undefined}
                  className={`${fieldInputClass} py-1.5`}
                />
                {errors.phones?.[index]?.phone?.message && (
                  <p className="text-xs text-red-600 mt-0.5">
                    {String(errors.phones[index]?.phone?.message)}
                  </p>
                )}
              </div>
            )}
          />
          <button
            type="button"
            onClick={() => removePhone(index)}
            className="p-1.5 text-slate-400 hover:text-red-600 rounded-lg"
            aria-label="Удалить телефон"
          >
            <Trash2 size={16} />
          </button>
        </div>
      ))}
    </div>
  </div>
);
