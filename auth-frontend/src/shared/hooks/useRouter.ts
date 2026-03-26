import { useState, useEffect, useMemo } from 'react';

export const useRouter = () => {
  const [path, setPath] = useState(window.location.pathname);
  const [search, setSearch] = useState(window.location.search);

  useEffect(() => {
    const handlePopState = () => {
      setPath(window.location.pathname);
      setSearch(window.location.search);
    };
    window.addEventListener('popstate', handlePopState);
    return () => window.removeEventListener('popstate', handlePopState);
  }, []);

  const navigate = (newPath: string) => {
    try {
      window.history.pushState({}, '', newPath);
    } catch {
      console.warn('History API pushState is restricted. Falling back to in-memory routing.');
    }
    const [p, s] = newPath.split('?');
    setPath(p || '/');
    setSearch(s ? `?${s}` : '');
  };

  const query = useMemo(() => new URLSearchParams(search), [search]);

  return { path, navigate, query };
};
