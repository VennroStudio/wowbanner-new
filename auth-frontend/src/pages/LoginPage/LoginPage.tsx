import { LoginForm } from '@/features/auth';

interface LoginPageProps {
  navigate: (path: string) => void;
}

export const LoginPage = ({ navigate }: LoginPageProps) => (
  <LoginForm navigate={navigate} />
);
