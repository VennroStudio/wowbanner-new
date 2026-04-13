import React, { useState } from 'react';
import { Header } from '@/widgets/header/ui/Header';
import { Sidebar } from '@/widgets/sidebar/ui/Sidebar';
import { MainPage } from '@/pages/main';
import { AuthInit, QueryProvider } from './providers';

const App = () => {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  const toggleSidebar = () => setIsSidebarOpen(!isSidebarOpen);

  return (
    <QueryProvider>
      <AuthInit>
        <div className="h-screen flex flex-col font-sans bg-slate-50 overflow-hidden">
          <Header toggleSidebar={toggleSidebar} />
          <div className="flex flex-1 overflow-hidden">
            <Sidebar isOpen={isSidebarOpen} />
            <main className="flex-1 overflow-y-auto">
              <MainPage />
            </main>
          </div>
        </div>
      </AuthInit>
    </QueryProvider>
  );
};

export default App;
