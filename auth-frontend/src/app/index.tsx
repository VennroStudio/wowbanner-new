import { AuthProvider } from './providers';
import { AppRouter } from './router';

const App = () => (
  <AuthProvider>
    <AppRouter />
  </AuthProvider>
);

export default App;
