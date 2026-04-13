import { Routes, Route } from 'react-router-dom';
import { Loader2 } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { ROUTES } from '@/shared/constants';
import {
  LoginPage,
  RegisterPage,
  VerifyEmailPage,
  ForgotPasswordPage,
  ResetPasswordPage,
  DashboardPage,
  AdminUsersPage,
  NotFoundPage,
} from '@/pages';

const ForgotPasswordRoute = () => {
  const { isAuthenticated } = useAuth();
  if (isAuthenticated) return <DashboardPage />;
  return <ForgotPasswordPage />;
};

const RegisterRoute = () => {
  const { isAuthenticated, isAdmin } = useAuth();
  if (!isAuthenticated) return <NotFoundPage />;
  if (!isAdmin) return <NotFoundPage type="403" />;
  return <RegisterPage />;
};

const AdminUsersRoute = () => {
  const { isAuthenticated, isAdmin } = useAuth();
  if (!isAuthenticated) return <NotFoundPage />;
  if (!isAdmin) return <NotFoundPage type="403" />;
  return <AdminUsersPage />;
};

const HomeRoute = () => {
  const { isAuthenticated } = useAuth();
  if (!isAuthenticated) return <LoginPage />;
  return <DashboardPage />;
};

const CatchAllRoute = () => {
  const { isAuthenticated } = useAuth();
  if (isAuthenticated) return <DashboardPage />;
  return <NotFoundPage />;
};

export const AppRouter = () => {
  const { isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <Loader2 className="animate-spin text-blue-600" size={48} />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-900">
      <Routes>
        <Route path={ROUTES.VERIFY_EMAIL} element={<VerifyEmailPage />} />
        <Route path={ROUTES.RESET_PASSWORD} element={<ResetPasswordPage />} />
        <Route path={ROUTES.FORGOT_PASSWORD} element={<ForgotPasswordRoute />} />
        <Route path={ROUTES.REGISTER} element={<RegisterRoute />} />
        <Route path={ROUTES.ADMIN_USERS} element={<AdminUsersRoute />} />
        <Route path={ROUTES.HOME} element={<HomeRoute />} />
        <Route path="*" element={<CatchAllRoute />} />
      </Routes>
    </div>
  );
};
