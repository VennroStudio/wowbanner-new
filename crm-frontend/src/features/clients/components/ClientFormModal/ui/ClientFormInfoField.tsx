import type { UseFormRegister } from 'react-hook-form';
import type { ClientFormValues } from '../lib/clientFormSchema';
import { fieldTextareaClass } from '../lib/formFieldClasses';

interface Props {
  register: UseFormRegister<ClientFormValues>;
}

export const ClientFormInfoField = ({ register }: Props) => (
  <div>
    <label className="block text-xs font-medium text-slate-500 mb-1">Комментарий</label>
    <textarea {...register('info')} rows={2} className={fieldTextareaClass} />
  </div>
);
