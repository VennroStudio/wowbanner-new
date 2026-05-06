import { Routes, Route } from 'react-router-dom';
import { ROUTES } from '@/shared/routing/routes';
import { HomePage } from '@/pages/home';
import { ClientsPage } from '@/pages/clients';
import { MaterialsPage } from '@/pages/materials';
import { ProcessingsPage } from '@/pages/processings';
import { PrintingsPage } from '@/pages/printings';
import { ProductsPage } from '@/pages/products';
import { CalculatorsPage } from '@/pages/calculators';
import { NotFoundPage } from '@/pages/not-found';

export const AppRouter = () => (
  <Routes>
    <Route path={ROUTES.HOME} element={<HomePage />} />
    <Route path={ROUTES.CLIENTS} element={<ClientsPage />} />
    <Route path={ROUTES.MATERIALS} element={<MaterialsPage />} />
    <Route path={ROUTES.PROCESSINGS} element={<ProcessingsPage />} />
    <Route path={ROUTES.PRINTINGS} element={<PrintingsPage />} />
    <Route path={ROUTES.PRODUCTS} element={<ProductsPage />} />
    <Route path={ROUTES.CALCULATORS} element={<CalculatorsPage />} />
    <Route path="*" element={<NotFoundPage />} />
  </Routes>
);
