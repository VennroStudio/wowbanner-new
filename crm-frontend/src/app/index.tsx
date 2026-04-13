import React, { useState } from 'react';
import { BrowserRouter } from 'react-router-dom';
import { Header } from '@/widgets/header/ui/Header';
import { Sidebar } from '@/widgets/sidebar/ui/Sidebar';
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
            <Header toggleSidebar={toggleSidebar} />
            <div className="flex flex-1 overflow-hidden">
              <Sidebar isOpen={isSidebarOpen} />
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
