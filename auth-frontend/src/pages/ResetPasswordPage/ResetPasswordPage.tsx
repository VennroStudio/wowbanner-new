import { useSearchParams } from 'react-router-dom';
import { ResetPasswordForm } from '@/features/auth';

export const ResetPasswordPage = () => {
  const [searchParams] = useSearchParams();
  const token = searchParams.get('token');
  return <ResetPasswordForm token={token} />;
};
