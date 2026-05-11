/** Как в ответе API: `role: { id, label }` */
export interface UserRoleRef {
  id: number;
  label: string;
}

export interface User {
  id: number;
  email: string;
  first_name: string;
  last_name: string;
  avatar: string | null;
  role: UserRoleRef;
  created_at: string;
  updated_at: string;
}

export interface UserSelectOption {
  id: number;
  name: string;
  email: string;
  first_name: string;
  last_name: string;
  role: UserRoleRef;
  status: UserRoleRef;
}
