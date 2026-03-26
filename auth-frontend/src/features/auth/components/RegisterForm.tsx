import React, { useState } from 'react';
import { Mail, Lock, UserIcon, CheckCircle2 } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { Input, Button, Alert, BackButton } from '@/shared/components';
import type { ApiError } from '@/shared/types';

interface RegisterFormProps {
  navigate: (path: string) => void;
}

export const RegisterForm: React.FC<RegisterFormProps> = ({ navigate }) => {
  const { apiFetch } = useAuth();
  const [form, setForm] = useState({ firstName: '', lastName: '', email: '', password: '' });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<ApiError | null>(null);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      await apiFetch('/users/create', { method: 'POST', body: JSON.stringify(form) });
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
        <h2 className="text-2xl font-bold text-slate-800 mb-2">Регистрация успешна!</h2>
        <p className="text-slate-600 mb-6">
          Мы отправили письмо на <strong>{form.email}</strong>. Перейдите по ссылке в письме для подтверждения
          аккаунта.
        </p>
        <Button onClick={() => navigate('/login')}>Вернуться ко входу</Button>
      </div>
    );
  }

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50">
      <BackButton onClick={() => navigate('/login')} />
      <h1 className="text-2xl font-bold text-slate-800 mb-6 text-center">Регистрация</h1>

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
        <Input
          label="Пароль"
          type="password"
          icon={Lock}
          value={form.password}
          onChange={(e) => setForm({ ...form, password: e.target.value })}
          error={fieldError('password')}
          required
        />
        <Button type="submit" isLoading={loading}>
          Создать аккаунт
        </Button>
      </form>

      <p className="text-center text-sm text-slate-500 mt-6">
        Уже есть аккаунт?{' '}
        <button onClick={() => navigate('/login')} className="text-blue-600 font-medium hover:underline">
          Войти
        </button>
      </p>
    </div>
  );
};
