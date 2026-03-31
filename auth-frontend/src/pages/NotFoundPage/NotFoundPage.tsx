import React from 'react';
import { FileQuestion, AlertCircle, Home, ArrowLeft } from 'lucide-react';
import { Button, PageCard, PageCardHeader } from '@/shared/components';
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
      <PageCardHeader
        icon={is403 ? AlertCircle : FileQuestion}
        accent={is403 ? 'amber' : 'slate'}
        title={is403 ? 'Доступ ограничен' : 'Страница не найдена'}
        description={
          is403
            ? 'У вас недостаточно прав для просмотра этой страницы. Пожалуйста, обратитесь к администратору.'
            : 'К сожалению, запрашиваемая страница не существует или была перемещена.'
        }
        className="mb-8"
      />

      <div className="space-y-3">
        <Button onClick={() => navigate(ROUTES.HOME)} className="w-full">
          <Home size={18} /> На главную
        </Button>

        <button
          type="button"
          onClick={() => window.history.back()}
          className="flex items-center justify-center gap-2 w-full py-3 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors"
        >
          <ArrowLeft size={16} /> Вернуться назад
        </button>
      </div>
    </PageCard>
  );
};
