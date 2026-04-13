import { Routes, Route } from 'react-router-dom';
import { ROUTES } from '@/shared/constants';
import { HomePage } from '@/pages/HomePage';
import { ClientsPage } from '@/pages/ClientsPage';
import { NotFoundPage } from '@/pages/NotFoundPage';

export const AppRouter = () => (
  <Routes>
    <Route path={ROUTES.HOME} element={<HomePage />} />
    <Route path={ROUTES.CLIENTS} element={<ClientsPage />} />
    <Route path="*" element={<NotFoundPage />} />
  </Routes>
);
