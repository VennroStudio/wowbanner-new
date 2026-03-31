import { Loader2 } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { useRouter } from '@/shared/hooks';
import { ROUTES } from '@/shared/constants';
import {
  LoginPage,
  RegisterPage,
  VerifyEmailPage,
  ForgotPasswordPage,
  ResetPasswordPage,
  DashboardPage,
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
    if (path === ROUTES.VERIFY_EMAIL)
      return <VerifyEmailPage token={query.get('token')} />;
    if (path === ROUTES.RESET_PASSWORD)
      return <ResetPasswordPage token={query.get('token')} />;

    if (isAuthenticated) {
      if (path === ROUTES.REGISTER)
        return isAdmin
            ? <RegisterPage />
            : <NotFoundPage type="403" />;
      return <DashboardPage />;
    }

    if (path === ROUTES.FORGOT_PASSWORD) return <ForgotPasswordPage />;
    if (path === ROUTES.HOME || path === '') return <LoginPage />;
    return <NotFoundPage />;
  };

  return (
      <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-900">
        {renderRoute()}
      </div>
  );
};
