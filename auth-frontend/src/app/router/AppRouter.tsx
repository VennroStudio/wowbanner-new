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
  const { path, query } = useRouter();
  const { isAuthenticated, isAdmin, isLoading } = useAuth();

  if (isLoading) {
    return (
        <div className="min-h-screen flex items-center justify-center bg-slate-50">
          <Loader2 className="animate-spin text-blue-600" size={48} />
        </div>
    );
  }

  const renderRoute = () => {
    if (path === '/email-verification')
      return <VerifyEmailPage token={query.get('token')} />;
    if (path === '/password-reset-confirm')
      return <ResetPasswordPage token={query.get('token')} />;

    if (isAuthenticated) {
      if (path === '/register')
        return isAdmin
            ? <RegisterPage />
            : <NotFoundPage type="403" />;
      return <AuthenticatedPage />;
    }

    if (path === '/forgot-password') return <ForgotPasswordPage />;
    if (path === '/' || path === '') return <LoginPage />;
    return <NotFoundPage />;
  };

  return (
      <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-900">
        {renderRoute()}
      </div>
  );
};
