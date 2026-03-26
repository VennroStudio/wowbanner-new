import { Loader2 } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { useRouter } from '@/shared/hooks';
import {
  LoginPage,
  RegisterPage,
  VerifyEmailPage,
  ForgotPasswordPage,
  ResetPasswordPage,
  AuthenticatedPage,
  NotFoundPage,
} from '@/pages';

export const AppRouter = () => {
  const { path, navigate, query } = useRouter();
  const { isAuthenticated, isAdmin, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <Loader2 className="animate-spin text-blue-600" size={48} />
      </div>
    );
  }

  const renderRoute = () => {
    // Публичные роуты — доступны всегда (приходят по ссылке из письма)
    if (path === '/email-verification') {
      return <VerifyEmailPage token={query.get('token')} navigate={navigate} />;
    }
    if (path === '/password-reset-confirm') {
      return <ResetPasswordPage token={query.get('token')} navigate={navigate} />;
    }

    // Роуты для авторизованных
    if (isAuthenticated) {
      if (path === '/register') {
        if (isAdmin) return <RegisterPage navigate={navigate} />;
        return <NotFoundPage navigate={navigate} type="403" />;
      }
      return <AuthenticatedPage navigate={navigate} />;
    }

    // Неавторизованные роуты
    switch (path) {
      case '/forgot-password':
        return <ForgotPasswordPage navigate={navigate} />;
      default:
        if (path === '/' || path === '') {
          if (isAuthenticated) return <AuthenticatedPage navigate={navigate} />;
          return <LoginPage navigate={navigate} />;
        }
        return <NotFoundPage navigate={navigate} />;
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-900">
      {renderRoute()}
    </div>
  );
};
