import type { Printing } from '@/entities/printing';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';

export const usePrintingsPage = () => useCrudListPageState<Printing>();
