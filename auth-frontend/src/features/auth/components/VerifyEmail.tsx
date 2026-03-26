import React, { useState, useEffect, useRef } from 'react';
import { Loader2, CheckCircle2, AlertCircle } from 'lucide-react';
import { useAuth, authApi } from '@/features/auth';
import { useRouter } from '@/shared/hooks';
import { Button } from '@/shared/components';

interface VerifyEmailProps {
  token: string | null;
}

export const VerifyEmail: React.FC<VerifyEmailProps> = ({ token }) => {
  const { navigate } = useRouter();
  const { apiFetch } = useAuth();
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>(() =>
    !token ? 'error' : 'loading',
  );
  const hasRun = useRef(false);

  useEffect(() => {
    if (!token || hasRun.current) return;
    hasRun.current = true;

    authApi.confirmEmail(apiFetch, token)
      .then(() => setStatus('success'))
      .catch(() => setStatus('error'));
  }, [apiFetch, token]);

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl text-center">
      {status === 'loading' && (
        <>
          <Loader2 className="animate-spin mx-auto text-blue-600 mb-4" size={32} />
          <p className="text-slate-500">Подтверждаем ваш email...</p>
        </>
      )}
      {status === 'success' && (
        <>
          <CheckCircle2 className="mx-auto text-emerald-500 mb-4" size={48} />
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Email подтверждён!</h2>
          <p className="text-slate-500 mb-6">Теперь вы можете войти в свой аккаунт.</p>
          <Button onClick={() => navigate('/login')}>Перейти ко входу</Button>
        </>
      )}
      {status === 'error' && (
        <>
          <AlertCircle className="mx-auto text-red-500 mb-4" size={48} />
          <h2 className="text-2xl font-bold text-slate-800 mb-2">Ошибка подтверждения</h2>
          <p className="text-slate-500 mb-6">Ссылка недействительна или устарела.</p>
          <Button variant="secondary" onClick={() => navigate('/login')}>
            Вернуться назад
          </Button>
        </>
      )}
    </div>
  );
};
