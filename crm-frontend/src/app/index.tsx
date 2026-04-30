import { useState } from 'react';
import { BrowserRouter } from 'react-router-dom';
import { AppHeader } from '@/widgets/app-header';
import { AppSidebar } from '@/widgets/app-sidebar';
import { AppRouter } from '@/app/router';
import { AuthInit, QueryProvider } from './providers';

const App = () => {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  const toggleSidebar = () => setIsSidebarOpen(!isSidebarOpen);

  return (
    <QueryProvider>
      <BrowserRouter>
        <AuthInit>
          <div className="h-screen flex flex-col font-sans bg-slate-50 overflow-hidden">
            <AppHeader toggleSidebar={toggleSidebar} />
            <div className="flex flex-1 overflow-hidden">
              <AppSidebar isOpen={isSidebarOpen} />
              <main className="flex-1 overflow-y-auto w-full min-w-0">
                <AppRouter />
              </main>
            </div>
          </div>
        </AuthInit>
      </BrowserRouter>
    </QueryProvider>
  );
};

export default App;
