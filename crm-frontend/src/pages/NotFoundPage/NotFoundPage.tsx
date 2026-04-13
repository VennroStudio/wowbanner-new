import React from 'react';
import { useNavigate } from 'react-router-dom';
import { FileQuestion, Home } from 'lucide-react';
import { ROUTES } from '@/shared/constants';

export const NotFoundPage = () => {
  const navigate = useNavigate();

  return (
    <div className="h-full flex flex-col items-center justify-center p-6 text-center">
      <FileQuestion className="text-slate-300 mb-4" size={56} strokeWidth={1.25} />
      <h1 className="text-xl font-semibold text-slate-800">Страница не найдена</h1>
      <p className="mt-2 text-slate-500 max-w-md">
        Запрашиваемый адрес не существует или был перемещён.
      </p>
      <button
        type="button"
        onClick={() => navigate(ROUTES.HOME)}
        className="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors"
      >
        <Home size={18} />
        На главную
      </button>
    </div>
  );
};
