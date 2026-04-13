import React from 'react';
import { Users } from 'lucide-react';

export const ClientsTableEmpty: React.FC = () => (
    <tr>
        <td colSpan={5} className="px-6 py-16 text-center">
            <Users size={36} className="mx-auto text-slate-200 mb-3" />
            <p className="text-sm font-medium text-slate-600">Клиенты не найдены</p>
            <p className="text-xs text-slate-400 mt-1">Попробуйте изменить запрос или добавьте нового клиента</p>
        </td>
    </tr>
);