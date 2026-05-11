export const userKeys = {
  all: ['users'] as const,
  select: (role?: number) => ['users', 'select', role ?? 0] as const,
};
