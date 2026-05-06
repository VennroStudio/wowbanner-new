import type { Product } from '@/entities/product';
import { useCrudListPageState } from '@/shared/lib/useCrudListPageState';

export const useProductsPage = () => useCrudListPageState<Product>();
