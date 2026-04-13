import React from 'react';
import { Plus, Download } from 'lucide-react';

interface ClientsHeaderProps {
    onAddClient?: () => void;
    onExport?: () => void;
    search: string;
    onSearchChange: (value: string) => void;
}

export const ClientsHeader: React.FC<ClientsHeaderProps> = ({
                                                                onAddClient,
                                                                onExport,
                                                                search,
                                                                onSearchChange,
                                                            }) => {
    return (
        <div className="flex items-center justify-between mb-6">
            <h1 className="text-xl font-semibold text-slate-900 tracking-tight">Клиенты</h1>

            <div className="flex items-center gap-3">
                <div className="relative">
                    <svg
                        className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                        width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                    >
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => onSearchChange(e.target.value)}
                        placeholder="Поиск по имени, email, телефону..."
                        className="pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-lg w-72
                       text-slate-700 placeholder-slate-400 outline-none
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10
                       hover:border-slate-300 transition-colors"
                    />
                </div>

                <button
                    onClick={onAddClient}
                    className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                     text-white text-sm font-medium rounded-lg transition-colors cursor-pointer"
                >
                    <Plus size={15} strokeWidth={2.5} />
                    Создать клиента
                </button>

                <button
                    onClick={onExport}
                    className="flex items-center gap-2 px-3 py-2 bg-white border border-slate-200
                     hover:bg-slate-50 hover:border-slate-300 text-slate-600 text-sm
                     font-medium rounded-lg transition-colors cursor-pointer"
                >
                    <Download size={14} />
                    Экспорт CSV
                </button>
            </div>
        </div>
    );
};