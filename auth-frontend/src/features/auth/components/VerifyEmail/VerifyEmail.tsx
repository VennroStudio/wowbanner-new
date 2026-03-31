import React, { useEffect } from 'react';
import { Loader2, CheckCircle2, AlertCircle } from 'lucide-react';
import { useConfirmEmailCommand } from '@/features/auth/hooks/useConfirmEmailCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Button, PageCard } from '@/shared/components';

interface VerifyEmailProps {
  token: string | null;
}

export const VerifyEmail: React.FC<VerifyEmailProps> = ({ token }) => {
  const { navigate } = useRouter();
  const confirmEmailMutation = useConfirmEmailCommand();
  const { mutate, isPending, isSuccess, isError } = confirmEmailMutation;

  useEffect(() => {
    if (token) {
      mutate(token);
    }
  }, [token, mutate]);

  return (
    <PageCard align="center">
      <div className="text-center">
        {isPending && token && (
          <>
            <Loader2 className="animate-spin mx-auto text-blue-600 mb-4" size={32} />
            <p className="text-slate-500">Подтверждаем ваш email...</p>
          </>
        )}
        {isSuccess && (
          <>
            <CheckCircle2 className="mx-auto text-emerald-500 mb-4" size={48} />
            <h2 className="text-2xl font-bold text-slate-800 mb-2">Email подтверждён!</h2>
            <p className="text-slate-500 mb-6">Теперь вы можете войти в свой аккаунт.</p>
            <Button onClick={() => navigate(ROUTES.HOME)}>Перейти ко входу</Button>
          </>
        )}
        {!isPending && !isSuccess && (isError || !token) && (
          <>
            <AlertCircle className="mx-auto text-red-500 mb-4" size={48} />
            <h2 className="text-2xl font-bold text-slate-800 mb-2">Ошибка подтверждения</h2>
            <p className="text-slate-500 mb-6">Ссылка недействительна или устарела.</p>
            <Button variant="secondary" onClick={() => navigate(ROUTES.HOME)}>
              Вернуться назад
            </Button>
          </>
        )}
      </div>
    </PageCard>
  );
};
