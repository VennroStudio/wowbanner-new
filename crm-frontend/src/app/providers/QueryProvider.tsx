import React from 'react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

// Создаем инстанс QueryClient с настройками по умолчанию
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false, // Отключаем автоматический рефетч при фокусе окна
      retry: 1, // Количество повторений при ошибке
      staleTime: 5 * 60 * 1000, // Данные считаются свежими 5 минут
    },
  },
});

export const QueryProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  return (
    <QueryClientProvider client={queryClient}>
      {children}
    </QueryClientProvider>
  );
};
