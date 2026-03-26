import { AuthenticatedView } from '@/features/auth';

interface AuthenticatedPageProps {
  navigate: (path: string) => void;
}

export const AuthenticatedPage = ({ navigate }: AuthenticatedPageProps) => (
  <AuthenticatedView navigate={navigate} />
);
