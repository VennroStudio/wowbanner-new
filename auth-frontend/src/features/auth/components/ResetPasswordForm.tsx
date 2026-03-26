import React, { useState } from 'react';
import { Lock, ArrowRight, AlertCircle, CheckCircle2 } from 'lucide-react';
import { useAuth } from '../hooks/useAuth';
import { Input, Button, Alert } from '@/shared/components';
import type { ApiError } from '@/shared/types';

interface ResetPasswordFormProps {
  token: string | null;
  navigate: (path: string) => void;
}

export const ResetPasswordForm: React.FC<ResetPasswordFormProps> = ({ token, navigate }) => {
  const { apiFetch } = useAuth();
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<ApiError | null>(null);
  const [success, setSuccess] = useState(false);

  // Нет токена — невалидная ссылка
  if (!token) {
    return (
      <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl text-center">
        <AlertCircle className="mx-auto text-red-500 mb-4" size={48} />
        <h2 className="text-2xl font-bold text-slate-800 mb-2">Недействительная ссылка</h2>
        <p className="text-slate-500 mb-6">Токен отсутствует или ссылка устарела.</p>
        <Button variant="secondary" onClick={() => navigate('/login')}>
          Вернуться назад
        </Button>
      </div>
    );
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      await apiFetch('/auth/password-reset/confirm', {
        method: 'POST',
        body: JSON.stringify({ token, password }),
      });
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
        <h2 className="text-2xl font-bold text-slate-800 mb-2">Пароль изменён!</h2>
        <p className="text-slate-600 mb-6">
          Теперь вы можете войти с новым паролем. Все старые сессии были завершены.
        </p>
        <Button onClick={() => navigate('/login')}>Перейти ко входу</Button>
      </div>
    );
  }

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50">
      <div className="text-center mb-8">
        <div className="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
          <Lock size={24} />
        </div>
        <h1 className="text-2xl font-bold text-slate-800">Новый пароль</h1>
        <p className="text-slate-500 text-sm mt-1">Придумайте надёжный пароль для вашего аккаунта</p>
      </div>

      <Alert message={error?.error?.message || ''} />

      <form onSubmit={handleSubmit}>
        <Input
          label="Новый пароль"
          type="password"
          icon={Lock}
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          error={fieldError('password')}
          required
        />
        <Button type="submit" isLoading={loading}>
          Сохранить пароль <ArrowRight size={18} />
        </Button>
      </form>
    </div>
  );
};
