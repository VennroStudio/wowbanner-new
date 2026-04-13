import React from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface Props {
    page: number;
    totalPages: number;
    total: number;
    onPageChange: (page: number) => void;
}

export const ClientsTablePagination: React.FC<Props> = ({ page, totalPages, total, onPageChange }) => (
    <div className="flex justify-center items-center gap-8 px-5 py-3.5 border-t border-slate-100 bg-white">
        <button
            disabled={page <= 1}
            onClick={() => onPageChange(page - 1)}
            className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600
                 bg-slate-50 border border-slate-200 rounded-lg
                 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed
                 transition-all cursor-pointer active:scale-95"
        >
            <ChevronLeft size={16} className="text-slate-400" />
            Назад
        </button>

        <div className="text-sm text-slate-500">
            Страница{' '}
            <span className="font-semibold text-slate-800">{page}</span>
            {' '}из{' '}
            <span className="font-semibold text-slate-800">{totalPages}</span>
            <span className="text-slate-400 ml-2">
        (всего: <span className="font-medium text-slate-500">{total}</span>)
      </span>
        </div>

        <button
            disabled={page >= totalPages}
            onClick={() => onPageChange(page + 1)}
            className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700
                 bg-white border border-slate-200 rounded-lg
                 hover:border-slate-300 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed
                 transition-all cursor-pointer active:scale-95"
        >
            Вперёд
            <ChevronRight size={16} className="text-slate-500" />
        </button>
    </div>
);