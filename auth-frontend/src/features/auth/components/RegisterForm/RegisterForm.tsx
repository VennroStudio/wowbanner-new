import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { Mail, UserIcon, CheckCircle2, UserRoundPlus } from 'lucide-react';
import { useRegisterCommand } from '@/features/auth/hooks/useRegisterCommand';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { Input, Button, Alert, PageCard, BackButton, PageCardHeader } from '@/shared/components';
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
      <PageCard align="center">
        <div className="text-center">
          <PageCardHeader
            icon={CheckCircle2}
            title="Успешно!"
            description="Приглашение отправлено."
            accent="emerald"
            className="mb-6"
          />
          <Button onClick={() => navigate(ROUTES.HOME)}>Вернуться</Button>
        </div>
      </PageCard>
    );
  }

  const serverError = (registerMutation.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <PageCard align="center">
      <BackButton onClick={() => navigate(ROUTES.HOME)} />

      <PageCardHeader
        icon={UserRoundPlus}
        title="Пригласить пользователя"
        description="Укажите данные — мы отправим приглашение на email"
      />

      <Alert message={serverError || ''} />

      <form onSubmit={handleSubmit((data) => registerMutation.mutate(data))} className="space-y-4 text-left">
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
    </PageCard>
  );
};
