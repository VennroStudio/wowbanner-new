import type { Processing } from '@/entities/processing';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';

export const useProcessingsPage = () => useCrudListPageState<Processing>();
