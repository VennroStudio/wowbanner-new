import { AppRouter } from './router';
import { RouterProvider } from '@/shared/lib/router/RouterProvider';
import { QueryProvider } from '@/app/providers';
import { AuthInit } from '@/app/providers';

const App = () => (
  <QueryProvider>
    <RouterProvider>
      <AuthInit>
        <AppRouter />
      </AuthInit>
    </RouterProvider>
  </QueryProvider>
);

export default App;
