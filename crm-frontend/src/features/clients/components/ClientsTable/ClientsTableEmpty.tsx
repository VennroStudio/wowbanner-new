import { Users } from 'lucide-react';
import { CLIENTS_TABLE_COLUMN_COUNT } from './constants';

export const ClientsTableEmpty = () => (
    <tr>
        <td colSpan={CLIENTS_TABLE_COLUMN_COUNT} className="px-6 py-16 text-center">
            <Users size={36} className="mx-auto text-slate-200 mb-3" />
            <p className="text-sm font-medium text-slate-600">Клиенты не найдены</p>
            <p className="text-xs text-slate-400 mt-1">Попробуйте изменить запрос или добавьте нового клиента</p>
        </td>
    </tr>
);