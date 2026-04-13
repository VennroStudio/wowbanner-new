import { useSearchParams } from 'react-router-dom';
import { VerifyEmail } from '@/features/auth';

export const VerifyEmailPage = () => {
  const [searchParams] = useSearchParams();
  const token = searchParams.get('token');
  return <VerifyEmail token={token} />;
};
