import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Lock, ArrowRight, AlertCircle, CheckCircle2 } from 'lucide-react';
import { useConfirmResetCommand } from '@/features/auth/hooks/useConfirmResetCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert } from '@/shared/components';
import { AuthLayout } from '@/features/auth/components/AuthLayout';
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
      <AuthLayout showLogo={false}>
        <div className="text-center">
          <AlertCircle className="mx-auto text-red-500 mb-4" size={48} />
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Недействительная ссылка</h2>
          <p className="text-slate-500 mb-6">Токен отсутствует или ссылка устарела.</p>
          <Button variant="secondary" onClick={() => navigate(ROUTES.HOME)}>
            Вернуться назад
          </Button>
        </div>
      </AuthLayout>
    );
  }

  if (resetMutation.isSuccess) {
    return (
      <AuthLayout showLogo={false}>
        <div className="text-center">
          <div className="bg-emerald-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600">
            <CheckCircle2 size={32} />
          </div>
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Пароль изменён!</h2>
          <p className="text-slate-600 mb-6">
            Теперь вы можете войти с новым паролем.
          </p>
          <Button onClick={() => navigate(ROUTES.HOME)}>Перейти ко входу</Button>
        </div>
      </AuthLayout>
    );
  }

  const serverError = (resetMutation.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <AuthLayout>
      <div className="text-center mb-8">
        <div className="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
          <Lock size={24} />
        </div>
        <h1 className="text-2xl font-bold text-slate-800">Новый пароль</h1>
        <p className="text-slate-500 text-sm mt-1">Придумайте надёжный пароль для вашего аккаунта</p>
      </div>

      <Alert message={serverError || ''} />

      <form onSubmit={handleSubmit((data) => resetMutation.mutate({ token: token!, password: data.password }))} className="space-y-4">
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
    </AuthLayout>
  );
};
