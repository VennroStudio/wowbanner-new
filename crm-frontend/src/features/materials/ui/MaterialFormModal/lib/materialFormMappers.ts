import type { Material } from '@/entities/material';
import type { CreateMaterialBody, UpdateMaterialBody } from '@/entities/material';
import type { MaterialFormValues } from './materialFormSchema';

export function mapMaterialToFormValues(material: Material): MaterialFormValues {
  return {
    name: material.name,
    description: material.description ?? '',
  };
}

export function buildCreateMaterialBody(values: MaterialFormValues): CreateMaterialBody {
  return {
    name: values.name.trim(),
    description: values.description?.trim() || undefined,
  };
}

export function buildUpdateMaterialBody(values: MaterialFormValues): UpdateMaterialBody {
  return {
    name: values.name.trim(),
    description: values.description?.trim() ?? '',
  };
}
