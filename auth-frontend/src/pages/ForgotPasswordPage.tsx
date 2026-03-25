import React, { useState } from 'react';
import { Mail, Lock, ArrowRight } from 'lucide-react';
import { useAuth } from '../context/AuthContext';
import { Input } from '../components/ui/Input';
import { Button } from '../components/ui/Button';
import { Alert } from '../components/ui/Alert';
import { BackButton } from '../components/ui/BackButton';
import type { ApiError } from '../types';

interface ForgotPasswordPageProps {
  navigate: (path: string) => void;
}

export const ForgotPasswordPage: React.FC<ForgotPasswordPageProps> = ({ navigate }) => {
  const { apiFetch } = useAuth();
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<ApiError | null>(null);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      await apiFetch('/auth/password-reset', { method: 'POST', body: JSON.stringify({ email }) });
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
        <div className="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
          <Mail size={32} />
        </div>
        <h2 className="text-2xl font-bold text-slate-800 mb-2">Письмо отправлено</h2>
        <p className="text-slate-600 mb-6">
          Мы отправили инструкции по сбросу пароля на <strong>{email}</strong>. Проверьте почту.
        </p>
        <Button variant="secondary" onClick={() => navigate('/login')}>
          Вернуться ко входу
        </Button>
      </div>
    );
  }

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50">
      <BackButton onClick={() => navigate('/login')} />

      <div className="text-center mb-8">
        <div className="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
          <Lock size={24} />
        </div>
        <h1 className="text-2xl font-bold text-slate-800">Восстановление пароля</h1>
        <p className="text-slate-500 text-sm mt-1">Введите email — мы пришлём ссылку для сброса</p>
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
        <Button type="submit" isLoading={loading}>
          Отправить письмо <ArrowRight size={18} />
        </Button>
      </form>
    </div>
  );
};
