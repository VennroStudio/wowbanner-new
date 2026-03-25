import React, { useState } from 'react';
import { Mail, Lock, ArrowRight } from 'lucide-react';
import { useAuth } from '../context/AuthContext';
import { Input } from '../components/ui/Input';
import { Button } from '../components/ui/Button';
import { Alert } from '../components/ui/Alert';
import type { ApiError } from '../types';

interface LoginPageProps {
  navigate: (path: string) => void;
}

export const LoginPage: React.FC<LoginPageProps> = ({ navigate }) => {
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
      const res = await apiFetch('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
      });
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
        <div className="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
          <Lock size={24} />
        </div>
        <h1 className="text-2xl font-bold text-slate-800">С возвращением</h1>
        <p className="text-slate-500 text-sm mt-1">Войдите в свой аккаунт Vennro</p>
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

        <div className="flex justify-end mb-6">
          <button
            type="button"
            onClick={() => navigate('/forgot-password')}
            className="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            Забыли пароль?
          </button>
        </div>

        <Button type="submit" isLoading={loading}>
          Войти <ArrowRight size={18} />
        </Button>
      </form>

      <p className="text-center text-sm text-slate-500 mt-6">
        Нет аккаунта?{' '}
        <button onClick={() => navigate('/register')} className="text-blue-600 font-medium hover:underline">
          Зарегистрироваться
        </button>
      </p>
    </div>
  );
};
