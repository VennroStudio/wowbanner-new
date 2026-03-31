import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Mail, UserIcon, CheckCircle2 } from 'lucide-react';
import { useRegisterCommand } from '@/features/auth/hooks/useRegisterCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert } from '@/shared/components';
import { AuthLayout } from '@/features/auth/components/AuthLayout';
import type { AxiosError } from 'axios';
import type { ApiError } from '@/shared/types';

const registerSchema = z.object({
  firstName: z.string().min(2, 'Имя слишком короткое'),
  lastName: z.string().min(2, 'Фамилия слишком короткая'),
  email: z.string().email('Некорректный email'),
});

type RegisterFormValues = z.infer<typeof registerSchema>;

export const RegisterForm: React.FC = () => {
  const { navigate } = useRouter();
  const registerMutation = useRegisterCommand();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<RegisterFormValues>({
    resolver: zodResolver(registerSchema),
  });

  if (registerMutation.isSuccess) {
    return (
      <AuthLayout showLogo={false}>
        <div className="text-center">
          <div className="bg-emerald-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600">
            <CheckCircle2 size={32} />
          </div>
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Успешно!</h2>
          <p className="text-slate-600 mb-6">
            Приглашение отправлено.
          </p>
          <Button onClick={() => navigate(ROUTES.HOME)}>Вернуться</Button>
        </div>
      </AuthLayout>
    );
  }

  const serverError = (registerMutation.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <AuthLayout title="Пригласить пользователя" onBack={() => navigate(ROUTES.HOME)}>
      <Alert message={serverError || ''} />

      <form onSubmit={handleSubmit((data) => registerMutation.mutate(data))} className="space-y-4">
        <div className="grid grid-cols-2 gap-4">
          <Input
            label="Имя"
            icon={UserIcon}
            {...register('firstName')}
            error={errors.firstName?.message}
            required
          />
          <Input
            label="Фамилия"
            {...register('lastName')}
            error={errors.lastName?.message}
            required
          />
        </div>
        <Input
          label="Email"
          type="email"
          icon={Mail}
          {...register('email')}
          error={errors.email?.message}
          required
        />
        <Button type="submit" isLoading={registerMutation.isPending}>
          Пригласить
        </Button>
      </form>
    </AuthLayout>
  );
};
