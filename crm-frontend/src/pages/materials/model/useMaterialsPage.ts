import type { Material } from '@/entities/material';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';

export const useMaterialsPage = () => useCrudListPageState<Material>();
