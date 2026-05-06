export type {
  Product,
  ProductMaterialLink,
  ProductPrintLink,
  ProductMaterialPayload,
  ProductPrintPayload,
  GetProductsParams,
  PaginatedProductsResponse,
} from './model/types';

export { productApi } from './api/product.api';
export type { CreateProductBody, UpdateProductBody } from './api/product.api';
export { productKeys } from './model/query-keys';

export { useProductsQuery } from './model/useProductsQuery';
export { useProductQuery } from './model/useProductQuery';
export { useCreateProductCommand } from './model/useCreateProductCommand';
export { useUpdateProductCommand } from './model/useUpdateProductCommand';
export { useDeleteProductCommand } from './model/useDeleteProductCommand';
