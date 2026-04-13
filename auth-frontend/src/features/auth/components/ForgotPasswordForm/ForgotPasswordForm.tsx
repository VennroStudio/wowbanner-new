import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Mail, Lock, ArrowRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useRequestResetCommand } from '@/features/auth/hooks/useRequestResetCommand';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert, PageCard, BackButton, PageCardHeader } from '@/shared/components';
import type { AxiosError } from 'axios';
import type { ApiError } from '@/shared/types';

const forgotPasswordSchema = z.object({
  email: z.string().email('Некорректный email'),
});

type ForgotPasswordValues = z.infer<typeof forgotPasswordSchema>;

export const ForgotPasswordForm: React.FC = () => {
  const navigate = useNavigate();
  const forgotMutation = useRequestResetCommand();

  const {
    register,
    handleSubmit,
    getValues,
    formState: { errors },
  } = useForm<ForgotPasswordValues>({
    resolver: zodResolver(forgotPasswordSchema),
  });

  if (forgotMutation.isSuccess) {
    const email = getValues('email');
    return (
      <PageCard align="center">
        <div className="text-center">
          <PageCardHeader
            icon={Mail}
            title="Письмо отправлено"
            description={
              <>
                Мы отправили инструкции по сбросу пароля на <strong className="text-slate-800">{email}</strong>.
                Проверьте почту.
              </>
            }
            className="mb-6"
          />
          <Button variant="secondary" onClick={() => navigate(ROUTES.HOME)}>
            Вернуться ко входу
          </Button>
        </div>
      </PageCard>
    );
  }

  const serverError = (forgotMutation.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <PageCard align="center">
      <BackButton onClick={() => navigate(ROUTES.HOME)} />

      <PageCardHeader
        icon={Lock}
        title="Восстановление пароля"
        description="Введите email — мы пришлём ссылку для сброса"
      />

      <Alert message={serverError || ''} />

      <form onSubmit={handleSubmit((data) => forgotMutation.mutate(data.email))} className="space-y-4 text-left">
        <Input
          label="Email"
          type="email"
          icon={Mail}
          {...register('email')}
          error={errors.email?.message}
          required
        />
        <Button type="submit" isLoading={forgotMutation.isPending}>
          Отправить письмо <ArrowRight size={18} />
        </Button>
      </form>
    </PageCard>
  );
};
