import type { Processing } from '@/entities/processing';
import type { CreateProcessingBody, UpdateProcessingBody } from '@/entities/processing';
import type { ProcessingFormValues } from './processingFormSchema';

export function mapProcessingToFormValues(processing: Processing): ProcessingFormValues {
  return {
    name: processing.name,
    description: processing.description ?? '',
    typeId: processing.type.id,
    costPrice: processing.cost_price ?? '',
    price: processing.price ?? '',
  };
}

export function buildCreateProcessingBody(values: ProcessingFormValues): CreateProcessingBody {
  return {
    name: values.name.trim(),
    description: values.description?.trim() || undefined,
    type: values.typeId,
    costPrice: values.costPrice.trim(),
    price: values.price.trim(),
  };
}

export function buildUpdateProcessingBody(values: ProcessingFormValues): UpdateProcessingBody {
  return buildCreateProcessingBody(values);
}
