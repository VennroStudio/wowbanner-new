import { RegisterForm } from '@/features/auth';

interface RegisterPageProps {
  navigate: (path: string) => void;
}

export const RegisterPage = ({ navigate }: RegisterPageProps) => (
  <RegisterForm navigate={navigate} />
);
