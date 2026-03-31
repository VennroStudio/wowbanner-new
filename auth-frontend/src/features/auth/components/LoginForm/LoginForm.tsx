import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Mail, Lock, ArrowRight } from 'lucide-react';
import { useLoginCommand } from '@/features/auth/hooks/useLoginCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert } from '@/shared/components';
import { AuthLayout } from '@/features/auth/components/AuthLayout';
import type { AxiosError } from 'axios';
import type { ApiError } from '@/shared/types';

const loginSchema = z.object({
  email: z.string().email('Некорректный email'),
  password: z.string().min(6, 'Пароль должен быть не менее 6 символов'),
});

type LoginFormValues = z.infer<typeof loginSchema>;

export const LoginForm: React.FC = () => {
  const { navigate } = useRouter();
  const login = useLoginCommand();
  
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginFormValues>({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = async (data: LoginFormValues) => {
    try {
      await login.mutateAsync(data);
      navigate(ROUTES.HOME);
    } catch (err) {
      console.error('Login error', err);
    }
  };

  const serverError = (login.error as AxiosError<ApiError>)?.response?.data?.error?.message;
  const fieldErrors = (login.error as AxiosError<ApiError>)?.response?.data?.validations;

  const getFieldError = (name: keyof LoginFormValues) => {
    const serverErr = fieldErrors?.find((v: { field: string; message: string }) => v.field === name)?.message;
    return errors[name]?.message || serverErr;
  };

  return (
    <AuthLayout>
      <Alert message={serverError || ''} />

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <Input
          label="Email"
          type="email"
          icon={Mail}
          {...register('email')}
          error={getFieldError('email')}
          required
        />
        <Input
          label="Пароль"
          type="password"
          icon={Lock}
          {...register('password')}
          error={getFieldError('password')}
          required
        />

        <Button type="submit" isLoading={login.isPending}>
          Войти <ArrowRight size={18} />
        </Button>

        <div className="flex justify-center mt-6">
          <button
            type="button"
            onClick={() => navigate(ROUTES.FORGOT_PASSWORD)}
            className="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            Забыли пароль?
          </button>
        </div>
      </form>
    </AuthLayout>
  );
};
