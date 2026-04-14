import { Routes, Route } from 'react-router-dom';
import { ROUTES } from '@/shared/constants';
import { HomePage } from '@/pages/HomePage';
import { ClientsPage } from '@/pages/ClientsPage';
import { MaterialsPage } from '@/pages/MaterialsPage';
import { ProcessingsPage } from '@/pages/ProcessingsPage';
import { NotFoundPage } from '@/pages/NotFoundPage';

export const AppRouter = () => (
  <Routes>
    <Route path={ROUTES.HOME} element={<HomePage />} />
    <Route path={ROUTES.CLIENTS} element={<ClientsPage />} />
    <Route path={ROUTES.MATERIALS} element={<MaterialsPage />} />
    <Route path={ROUTES.PROCESSINGS} element={<ProcessingsPage />} />
    <Route path="*" element={<NotFoundPage />} />
  </Routes>
);
