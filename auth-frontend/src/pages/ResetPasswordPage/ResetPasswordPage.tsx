import { ResetPasswordForm } from '@/features/auth';

interface ResetPasswordPageProps {
  token: string | null;
  navigate: (path: string) => void;
}

export const ResetPasswordPage = ({ token, navigate }: ResetPasswordPageProps) => (
  <ResetPasswordForm token={token} navigate={navigate} />
);
