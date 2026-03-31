import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Lock, ArrowRight, AlertCircle, CheckCircle2 } from 'lucide-react';
import { useConfirmResetCommand } from '@/features/auth/hooks/useConfirmResetCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert, PageCard, PageCardHeader } from '@/shared/components';
import type { AxiosError } from 'axios';
import type { ApiError } from '@/shared/types';

const resetPasswordSchema = z.object({
  password: z.string().min(6, 'Пароль должен быть не менее 6 символов'),
});

type ResetPasswordValues = z.infer<typeof resetPasswordSchema>;

interface ResetPasswordFormProps {
  token: string | null;
}

export const ResetPasswordForm: React.FC<ResetPasswordFormProps> = ({ token }) => {
  const { navigate } = useRouter();
  const resetMutation = useConfirmResetCommand();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<ResetPasswordValues>({
    resolver: zodResolver(resetPasswordSchema),
  });

  if (!token) {
    return (
      <PageCard align="center">
        <div className="text-center">
          <AlertCircle className="mx-auto text-red-500 mb-4" size={48} />
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Недействительная ссылка</h2>
          <p className="text-slate-500 mb-6">Токен отсутствует или ссылка устарела.</p>
          <Button variant="secondary" onClick={() => navigate(ROUTES.HOME)}>
            Вернуться назад
          </Button>
        </div>
      </PageCard>
    );
  }

  if (resetMutation.isSuccess) {
    return (
      <PageCard align="center">
        <div className="text-center">
          <PageCardHeader
            icon={CheckCircle2}
            title="Пароль изменён!"
            description="Теперь вы можете войти с новым паролем."
            accent="emerald"
            className="mb-6"
          />
          <Button onClick={() => navigate(ROUTES.HOME)}>Перейти ко входу</Button>
        </div>
      </PageCard>
    );
  }

  const serverError = (resetMutation.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <PageCard align="center">
      <PageCardHeader
        icon={Lock}
        title="Новый пароль"
        description="Придумайте надёжный пароль для вашего аккаунта"
      />

      <Alert message={serverError || ''} />

      <form
        onSubmit={handleSubmit((data) => resetMutation.mutate({ token: token!, password: data.password }))}
        className="space-y-4 text-left"
      >
        <Input
          label="Новый пароль"
          type="password"
          icon={Lock}
          {...register('password')}
          error={errors.password?.message}
          required
        />
        <Button type="submit" isLoading={resetMutation.isPending}>
          Сохранить пароль <ArrowRight size={18} />
        </Button>
      </form>
    </PageCard>
  );
};
