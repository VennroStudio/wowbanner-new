import { ResetPasswordForm } from '@/features/auth';

interface ResetPasswordPageProps {
  token: string | null;
}

export const ResetPasswordPage = ({ token }: ResetPasswordPageProps) => (
  <ResetPasswordForm token={token} />
);
