import { AuthProvider } from './providers';
import { AppRouter } from './router';
import { RouterProvider } from '@/shared/lib/router/RouterProvider';

const App = () => (
  <RouterProvider>
    <AuthProvider>
      <AppRouter />
    </AuthProvider>
  </RouterProvider>
);

export default App;
