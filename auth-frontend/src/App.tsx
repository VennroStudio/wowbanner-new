import { Loader2 } from 'lucide-react';
import { AuthProvider, useAuth } from './context/AuthContext';
import { useRouter } from './hooks/useRouter';
import { LoginPage } from './pages/LoginPage';
import { RegisterPage } from './pages/RegisterPage';
import { VerifyEmailPage } from './pages/VerifyEmailPage';
import { ForgotPasswordPage } from './pages/ForgotPasswordPage';
import { ResetPasswordPage } from './pages/ResetPasswordPage';
import { AuthenticatedPage } from './pages/AuthenticatedPage';

const AppContent = () => {
  const { path, navigate, query } = useRouter();
  const { isAuthenticated, isLoading } = useAuth();

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

    // Если уже авторизован — показываем заглушку (основной app на другом домене)
    if (isAuthenticated) {
      return <AuthenticatedPage navigate={navigate} />;
    }

    // Неавторизованные роуты
    switch (path) {
      case '/register':
        return <RegisterPage navigate={navigate} />;
      case '/forgot-password':
        return <ForgotPasswordPage navigate={navigate} />;
      default:
        return <LoginPage navigate={navigate} />;
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-900">
      {renderRoute()}
    </div>
  );
};

export default function App() {
  return (
    <AuthProvider>
      <AppContent />
    </AuthProvider>
  );
}
