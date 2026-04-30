import { useState, useRef, useEffect } from 'react';
import { sessionApi, useSessionStore } from '@/entities/session';
import { AUTH_URL } from '@/shared/config/env';
import { UserDropdownTrigger } from './UserDropdownTrigger';
import { UserDropdownMenu } from './UserDropdownMenu';

export const UserDropdown = () => {
  const { user, logout } = useSessionStore();
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const displayName = user
    ? `${user.first_name} ${user.last_name}`.trim()
    : 'Пользователь';

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleLogout = async () => {
    try {
      await sessionApi.logout();
    } catch {
      // ignore
    } finally {
      logout();
      window.location.href = AUTH_URL;
    }
  };

  return (
    <div className="relative" ref={dropdownRef}>
      <UserDropdownTrigger
        avatarUrl={user?.avatar}
        displayName={displayName}
        onClick={() => setIsOpen(!isOpen)}
      />
      {isOpen && (
        <UserDropdownMenu role={user?.role.label ?? ''} onLogout={handleLogout} />
      )}
    </div>
  );
};
