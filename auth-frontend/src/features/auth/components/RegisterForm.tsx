import React, { useState } from 'react';
import { Mail, UserIcon, CheckCircle2 } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import { userApi } from '@/entities/user';
import { Input, Button, Alert, BackButton } from '@/shared/components';
import type { ApiError } from '@/shared/types';

export const RegisterForm: React.FC = () => {
  const { navigate } = useRouter();
  const { apiFetch } = useAuth();
  const [form, setForm] = useState({ firstName: '', lastName: '', email: ''});
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<ApiError | null>(null);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      await userApi.register(apiFetch, form);
      setSuccess(true);
    } catch (err: unknown) {
      setError(err as ApiError);
    } finally {
      setLoading(false);
    }
  };

  const fieldError = (field: string) => error?.validations?.find((v) => v.field === field)?.message;

  if (success) {
    return (
      <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 text-center">
        <div className="bg-emerald-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600">
          <CheckCircle2 size={32} />
        </div>
        <h2 className="text-2xl font-bold text-slate-800 mb-2">Приглашение успешно отправлено!</h2>
        <p className="text-slate-600 mb-6">
          Мы отправили письмо на <strong>{form.email}</strong>. Для использования аккаунта пользователю необходимо подтвердить свой аккаунт в письме.
        </p>
        <Button onClick={() => navigate(ROUTES.HOME)}>Вернуться в панель управления</Button>
      </div>
    );
  }

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50">
      <BackButton onClick={() => navigate(ROUTES.HOME)} />
      <h1 className="text-2xl font-bold text-slate-800 mb-6 text-center">Пригласить пользователя</h1>

      <Alert message={error?.error?.message || ''} />

      <form onSubmit={handleSubmit}>
        <div className="grid grid-cols-2 gap-4">
          <Input
            label="Имя"
            icon={UserIcon}
            value={form.firstName}
            onChange={(e) => setForm({ ...form, firstName: e.target.value })}
            error={fieldError('firstName')}
            required
          />
          <Input
            label="Фамилия"
            value={form.lastName}
            onChange={(e) => setForm({ ...form, lastName: e.target.value })}
            error={fieldError('lastName')}
            required
          />
        </div>
        <Input
          label="Email"
          type="email"
          icon={Mail}
          value={form.email}
          onChange={(e) => setForm({ ...form, email: e.target.value })}
          error={fieldError('email')}
          required
        />
        <Button type="submit" isLoading={loading}>
          Пригласить
        </Button>
      </form>

    </div>
  );
};
