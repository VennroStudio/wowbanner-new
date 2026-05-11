import type { Product } from '@/entities/product';
import type { CreateProductBody, UpdateProductBody } from '@/entities/product';
import type { ProductFormValues } from './productFormSchema';

export const mapProductToFormValues = (product: Product): ProductFormValues => ({
  name: product.name,
  materials: (product.materials ?? []).map((item) => ({
    id: item.id,
    materialId: item.material_id ?? 0,
    materialName: item.material_name,
    materialOptionId: item.material_option_id,
    materialOptionName: item.material_option_name,
  })),
  prints: (product.prints ?? []).map((item) => ({
    id: item.id,
    printId: item.print_id,
    printName: item.print_name,
  })),
});

const buildProductBody = (values: ProductFormValues) => ({
  name: values.name.trim(),
  materials: values.materials.map((item) => ({
    ...(item.id ? { id: item.id } : {}),
    materialId: item.materialId,
    materialOptionId: item.materialOptionId,
  })),
  prints: values.prints.map((item) => ({
    ...(item.id ? { id: item.id } : {}),
    printId: item.printId,
  })),
});

export const buildCreateProductBody = (values: ProductFormValues): CreateProductBody =>
  buildProductBody(values);

export const buildUpdateProductBody = (values: ProductFormValues): UpdateProductBody =>
  buildProductBody(values);
