import React, { useState } from 'react';
import { Mail, Lock, ArrowRight } from 'lucide-react';
import { useAuth, authApi } from '@/features/auth';
import { Input, Button, Alert } from '@/shared/components';
import type { ApiError } from '@/shared/types';

interface LoginFormProps {
  navigate: (path: string) => void;
}

export const LoginForm: React.FC<LoginFormProps> = ({ navigate }) => {
  const { apiFetch, login } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<ApiError | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const res = (await authApi.login(apiFetch, { email, password })) as {
        data: { access_token: string };
      };
      await login(res.data.access_token);
      navigate('/');
    } catch (err: unknown) {
      setError(err as ApiError);
    } finally {
      setLoading(false);
    }
  };

  const fieldError = (field: string) => error?.validations?.find((v) => v.field === field)?.message;

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50">
      <div className="text-center mb-8">
        <div className="bg-blue-50 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-4">
          <img src="https://storage.vennro.ru/vs-project/assets/logo-wowbanner.png" alt="Логотип" className="src"/>
        </div>
      </div>

      <Alert message={error?.error?.message || ''} />

      <form onSubmit={handleSubmit}>
        <Input
          label="Email"
          type="email"
          icon={Mail}
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          error={fieldError('email')}
          required
        />
        <Input
          label="Пароль"
          type="password"
          icon={Lock}
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          error={fieldError('password')}
          required
        />

        <Button type="submit" isLoading={loading}>
          Войти <ArrowRight size={18} />
        </Button>

        <div className="flex justify-center mt-6">
          <button
              type="button"
              onClick={() => navigate('/forgot-password')}
              className="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            Забыли пароль?
          </button>
        </div>

      </form>
    </div>
  );
};
