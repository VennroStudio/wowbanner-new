import type { FieldErrors, UseFormRegister } from 'react-hook-form';
import type { ClientFormValues } from '../lib/clientFormSchema';
import { fieldInputClass } from '@/shared/ui';

interface Props {
  register: UseFormRegister<ClientFormValues>;
  errors: FieldErrors<ClientFormValues>;
}

export const ClientFormIdentityFields = ({ register, errors }: Props) => (
  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
      <label className="block text-xs font-medium text-slate-500 mb-1">Фамилия *</label>
      <input {...register('lastName')} className={fieldInputClass} />
      {errors.lastName && (
        <p className="text-xs text-red-600 mt-0.5">{errors.lastName.message}</p>
      )}
    </div>
    <div>
      <label className="block text-xs font-medium text-slate-500 mb-1">Имя *</label>
      <input {...register('firstName')} className={fieldInputClass} />
      {errors.firstName && (
        <p className="text-xs text-red-600 mt-0.5">{errors.firstName.message}</p>
      )}
    </div>
    <div>
      <label className="block text-xs font-medium text-slate-500 mb-1">Отчество</label>
      <input {...register('middleName')} className={fieldInputClass} />
    </div>
    <div>
      <label className="block text-xs font-medium text-slate-500 mb-1">Email</label>
      <input type="email" {...register('email')} className={fieldInputClass} />
      {errors.email && <p className="text-xs text-red-600 mt-0.5">{errors.email.message}</p>}
    </div>
  </div>
);
