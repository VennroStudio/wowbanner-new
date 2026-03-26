import { VerifyEmail } from '@/features/auth';

interface VerifyEmailPageProps {
  token: string | null;
  navigate: (path: string) => void;
}

export const VerifyEmailPage = ({ token, navigate }: VerifyEmailPageProps) => (
  <VerifyEmail token={token} navigate={navigate} />
);
