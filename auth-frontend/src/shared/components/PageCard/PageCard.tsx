import React from 'react';

/** Единая ширина «окна» для всех страниц (не для модалок). */
const PAGE_MAX_WIDTH = 'max-w-3xl';

interface PageCardProps {
  children: React.ReactNode;
  /** По умолчанию слева; для дашборда и экранов с центрированным контентом — center */
  align?: 'left' | 'center';
  className?: string;
}

export const PageCard: React.FC<PageCardProps> = ({
  children,
  align = 'left',
  className,
}) => {
  const alignClass = align === 'center' ? 'text-center' : 'text-left';
  return (
    <div
      className={[
        'w-full',
        PAGE_MAX_WIDTH,
        'bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50',
        alignClass,
        className,
      ]
        .filter(Boolean)
        .join(' ')}
    >
      {children}
    </div>
  );
};
