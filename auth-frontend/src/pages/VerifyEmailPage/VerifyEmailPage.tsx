import { VerifyEmail } from '@/features/auth';

interface VerifyEmailPageProps {
  token: string | null;
}

export const VerifyEmailPage = ({ token }: VerifyEmailPageProps) => (
  <VerifyEmail token={token} />
);
