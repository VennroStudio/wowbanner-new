import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Mail, Lock, ArrowRight } from 'lucide-react';
import { useRequestResetCommand } from '@/features/auth/hooks/useRequestResetCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert } from '@/shared/components';
import { AuthLayout } from '@/features/auth/components/AuthLayout';
import type { AxiosError } from 'axios';
import type { ApiError } from '@/shared/types';

const forgotPasswordSchema = z.object({
  email: z.string().email('Некорректный email'),
});

type ForgotPasswordValues = z.infer<typeof forgotPasswordSchema>;

export const ForgotPasswordForm: React.FC = () => {
  const { navigate } = useRouter();
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
      <AuthLayout showLogo={false}>
        <div className="text-center">
          <div className="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
            <Mail size={32} />
          </div>
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Письмо отправлено</h2>
          <p className="text-slate-600 mb-6">
            Мы отправили инструкции по сбросу пароля на <strong>{email}</strong>. Проверьте почту.
          </p>
          <Button variant="secondary" onClick={() => navigate(ROUTES.HOME)}>
            Вернуться ко входу
          </Button>
        </div>
      </AuthLayout>
    );
  }

  const serverError = (forgotMutation.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <AuthLayout onBack={() => navigate(ROUTES.HOME)}>
      <div className="text-center mb-8">
        <div className="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
          <Lock size={24} />
        </div>
        <h1 className="text-2xl font-bold text-slate-800">Восстановление пароля</h1>
        <p className="text-slate-500 text-sm mt-1">Введите email — мы пришлём ссылку для сброса</p>
      </div>

      <Alert message={serverError || ''} />

      <form onSubmit={handleSubmit((data) => forgotMutation.mutate(data.email))} className="space-y-4">
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
    </AuthLayout>
  );
};
