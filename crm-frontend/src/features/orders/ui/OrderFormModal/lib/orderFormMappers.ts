import type { CreateOrderBody } from '@/entities/order';
import type { OrderFormValues } from './orderFormSchema';

const formatDateTimeForApi = (value: string) => {
  if (!value) return value;

  return value.length === 16 ? `${value.replace('T', ' ')}:00` : value.replace('T', ' ');
};

export const buildCreateOrderBody = (
  values: OrderFormValues,
  files: File[],
): CreateOrderBody => {
  return {
    clientId: Number(values.clientId),
    managerId: values.managerId ? Number(values.managerId) : null,
    designerId: values.designerId ? Number(values.designerId) : null,
    statusType: Number(values.statusType),
    storageType: Number(values.storageType),
    acceptedAt: formatDateTimeForApi(values.acceptedAt),
    deadlineAt: formatDateTimeForApi(values.deadlineAt),
    generalNote: values.generalNote.trim() || null,
    extension: values.extension.trim() || null,
    delivery: values.hasDelivery && values.deliveryType
      ? {
          deliveryType: Number(values.deliveryType),
          address: values.deliveryAddress.trim() || null,
          comment: values.deliveryComment.trim() || null,
        }
      : null,
    sections: values.sections.map((sectionType) => ({
      sectionType: Number(sectionType),
    })),
    services: values.services.map((service) => ({
      serviceType: Number(service.serviceType),
      price: service.price.trim(),
      note: service.note?.trim() || null,
    })),
    files,
    fileOriginalNames: files.map((file) => file.name),
  };
};
