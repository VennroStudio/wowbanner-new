export interface User {
  id: number;
  email: string;
  first_name: string | null;
  last_name: string | null;
  avatar: string | null;
  role: string | null;
  created_at: string;
  updated_at: string;
}
