import { ForgotPasswordForm } from '@/features/auth';

interface ForgotPasswordPageProps {
  navigate: (path: string) => void;
}

export const ForgotPasswordPage = ({ navigate }: ForgotPasswordPageProps) => (
  <ForgotPasswordForm navigate={navigate} />
);
