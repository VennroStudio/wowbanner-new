import React from 'react';
import { FileQuestion, AlertCircle, Home, ArrowLeft } from 'lucide-react';
import { Button, PageCard } from '@/shared/components';
import { ROUTES } from '@/shared/constants';
import { useRouter } from '@/shared/hooks';

interface NotFoundPageProps {
  type?: '404' | '403';
}

export const NotFoundPage: React.FC<NotFoundPageProps> = ({ type = '404' }) => {
  const { navigate } = useRouter();
  const is403 = type === '403';

  return (
    <PageCard align="center">
      <div className={`w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 ${
        is403 ? 'bg-amber-50 text-amber-500' : 'bg-slate-50 text-slate-400'
      }`}>
        {is403 ? <AlertCircle size={40} /> : <FileQuestion size={40} />}
      </div>

      <h1 className="text-3xl font-bold text-slate-800 mb-2">
        {is403 ? 'Доступ ограничен' : 'Страница не найдена'}
      </h1>
      
      <p className="text-slate-500 mb-8">
        {is403 
          ? 'У вас недостаточно прав для просмотра этой страницы. Пожалуйста, обратитесь к администратору.' 
          : 'К сожалению, запрашиваемая страница не существует или была перемещена.'}
      </p>

      <div className="space-y-3">
        <Button onClick={() => navigate(ROUTES.HOME)} className="w-full">
          <Home size={18} /> На главную
        </Button>
        
        <button 
          onClick={() => window.history.back()}
          className="flex items-center justify-center gap-2 w-full py-3 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors"
        >
          <ArrowLeft size={16} /> Вернуться назад
        </button>
      </div>
    </PageCard>
  );
};
