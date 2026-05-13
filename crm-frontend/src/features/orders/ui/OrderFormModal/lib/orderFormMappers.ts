import type { CreateOrderBody, Order, UpdateOrderBody } from '@/entities/order';
import type { OrderFormValues } from './orderFormSchema';

const formatDateTimeForApi = (value: string) => {
  if (!value) return value;

  return value.length === 16 ? `${value.replace('T', ' ')}:00` : value.replace('T', ' ');
};

const formatDateTimeForInput = (value: string | null | undefined) => {
  if (!value) return '';

  return value.replace(' ', 'T').slice(0, 16);
};

const toStringId = (value: number | string | null | undefined) => (
  value == null ? '' : String(value)
);

const buildOrderBodyBase = (
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
          id: values.deliveryId ? Number(values.deliveryId) : undefined,
          deliveryType: Number(values.deliveryType),
          address: values.deliveryAddress.trim() || null,
          comment: values.deliveryComment.trim() || null,
        }
      : null,
    items: values.items.map((item) => ({
      id: item.id ? Number(item.id) : undefined,
      sourceItemId: item.sourceItemId ? Number(item.sourceItemId) : null,
      printId: Number(item.printId),
      productId: Number(item.productId),
      materialId: Number(item.materialId),
      optionId: Number(item.optionId),
      dpiType: Number(item.dpiType),
      variantType: Number(item.variantType),
      width: item.width.trim(),
      height: item.height.trim(),
      quantity: Number(item.quantity),
      price: item.price.trim(),
      performerId: item.performerId ? Number(item.performerId) : null,
      note: item.note.trim() || null,
      printed: item.printed,
      ready: item.ready,
      processings: item.processings.map((processingId) => ({
        processingId: Number(processingId),
      })),
    })),
    sections: values.sections.map((sectionType) => ({
      sectionType: Number(sectionType),
    })),
    services: values.services.map((service) => ({
      id: service.id ? Number(service.id) : undefined,
      serviceType: Number(service.serviceType),
      price: service.price.trim(),
      note: service.note?.trim() || null,
    })),
    files,
    fileOriginalNames: files.map((file) => file.name),
  };
};

export const buildCreateOrderBody = (
  values: OrderFormValues,
  files: File[],
): CreateOrderBody => buildOrderBodyBase(values, files);

export const buildUpdateOrderBody = (
  values: OrderFormValues,
  files: File[],
  keepFileIds: number[],
): UpdateOrderBody => ({
  ...buildOrderBodyBase(values, files),
  keepFileIds,
});

export const mapOrderToFormValues = (order: Order): OrderFormValues => ({
  clientId: String(order.client_id),
  managerId: toStringId(order.manager_id),
  designerId: toStringId(order.designer_id),
  statusType: String(order.status_type.id),
  storageType: String(order.storage_type.id),
  acceptedAt: formatDateTimeForInput(order.accepted_at),
  deadlineAt: formatDateTimeForInput(order.deadline_at),
  generalNote: order.general_note ?? '',
  extension: order.extension ?? '',
  sections: order.sections.map((section) => String(section.section_type.id)),
  hasDelivery: order.delivery != null,
  deliveryId: toStringId(order.delivery?.id),
  deliveryType: toStringId(order.delivery?.delivery_type.id),
  deliveryAddress: order.delivery?.address ?? '',
  deliveryComment: order.delivery?.comment ?? '',
  items: order.items.map((item) => ({
    id: String(item.id),
    sourceItemId: toStringId(item.source_item_id),
    printId: String(item.print_id),
    productId: String(item.product_id),
    materialId: String(item.material_id),
    optionId: String(item.option_id),
    dpiType: String(item.dpi_type.id),
    variantType: String(item.variant_type.id),
    width: item.width,
    height: item.height,
    quantity: String(item.quantity),
    price: item.price,
    performerId: toStringId(item.performer_id),
    note: item.note ?? '',
    printed: item.printed,
    ready: item.ready,
    processings: item.processings.map((processing) => String(processing.processing_id)),
  })),
  services: order.services.map((service) => ({
    id: String(service.id),
    serviceType: String(service.service_type.id),
    price: service.price,
    note: service.note ?? '',
  })),
});
