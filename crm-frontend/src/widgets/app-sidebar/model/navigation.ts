import type { LucideIcon } from 'lucide-react';
import {
  Home,
  Users,
  Briefcase,
  BarChart2,
  Settings,
  MessageSquare,
  Layers,
  Wrench,
  Printer,
} from 'lucide-react';
import { ROUTES } from '@/shared/routing/routes';

type NavigationLink = { to: string; icon: LucideIcon; label: string };
type NavigationDisabled = { disabled: true; icon: LucideIcon; label: string };

export type NavigationItem = NavigationLink | NavigationDisabled;

export const navigationItems: NavigationItem[] = [
  { to: ROUTES.HOME, icon: Home, label: 'Главная' },
  { to: ROUTES.CLIENTS, icon: Users, label: 'Клиенты' },
  { to: ROUTES.MATERIALS, icon: Layers, label: 'Материалы' },
  { to: ROUTES.PROCESSINGS, icon: Wrench, label: 'Доп. обработки' },
  { to: ROUTES.PRINTINGS, icon: Printer, label: 'Тип печати' },
  { disabled: true, icon: Briefcase, label: 'Заказы' },
  { disabled: true, icon: BarChart2, label: 'Аналитика' },
  { disabled: true, icon: MessageSquare, label: 'Чаты' },
  { disabled: true, icon: Settings, label: 'Настройки' },
];
