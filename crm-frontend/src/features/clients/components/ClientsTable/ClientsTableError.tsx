import { AlertCircle } from 'lucide-react';
import { CLIENTS_TABLE_COLUMN_COUNT } from './constants';

export const ClientsTableError = () => (
    <tr>
        <td colSpan={CLIENTS_TABLE_COLUMN_COUNT} className="px-6 py-12 text-center">
            <AlertCircle size={32} className="mx-auto text-red-300 mb-3" />
            <p className="text-sm font-medium text-red-500">Ошибка загрузки клиентов</p>
            <p className="text-xs text-slate-400 mt-1">Попробуйте обновить страницу</p>
        </td>
    </tr>
);