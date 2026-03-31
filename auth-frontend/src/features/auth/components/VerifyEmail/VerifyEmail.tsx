import React, { useEffect } from 'react';
import { Loader2, CheckCircle2, AlertCircle } from 'lucide-react';
import { useConfirmEmailCommand } from '@/features/auth/hooks/useConfirmEmailCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Button, PageCard, PageCardHeader } from '@/shared/components';

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
            <PageCardHeader
              icon={CheckCircle2}
              accent="emerald"
              title="Email подтверждён!"
              description="Теперь вы можете войти в свой аккаунт."
              className="mb-6"
            />
            <Button onClick={() => navigate(ROUTES.HOME)}>Перейти ко входу</Button>
          </>
        )}
        {!isPending && !isSuccess && (isError || !token) && (
          <>
            <PageCardHeader
              icon={AlertCircle}
              accent="red"
              title="Ошибка подтверждения"
              description="Ссылка недействительна или устарела."
              className="mb-6"
            />
            <Button variant="secondary" onClick={() => navigate(ROUTES.HOME)}>
              Вернуться назад
            </Button>
          </>
        )}
      </div>
    </PageCard>
  );
};
