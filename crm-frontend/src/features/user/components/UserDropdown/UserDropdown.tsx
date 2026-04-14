import React, { useState, useRef, useEffect } from 'react';
import { useAuthStore } from '@/features/auth';
import { authApi } from '@/entities/user';
import { AUTH_URL } from '@/shared/constants';
import { UserDropdownTrigger } from './UserDropdownTrigger';
import { UserDropdownMenu } from './UserDropdownMenu';

export const UserDropdown = () => {
  const { user, logout } = useAuthStore();
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
      await authApi.logout();
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
