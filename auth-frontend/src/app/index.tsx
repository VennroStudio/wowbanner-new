import { BrowserRouter } from 'react-router-dom';
import { AppRouter } from './router';
import { QueryProvider } from '@/app/providers';
import { AuthInit } from '@/app/providers';

const App = () => (
  <QueryProvider>
    <BrowserRouter>
      <AuthInit>
        <AppRouter />
      </AuthInit>
    </BrowserRouter>
  </QueryProvider>
);

export default App;
