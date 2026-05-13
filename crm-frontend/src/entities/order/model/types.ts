import type { PaginatedResponse } from '@/shared/api/types';

export interface OrderEnumRef {
  id: number;
  label: string;
}

export interface OrderEntitySummary {
  id: number;
  name: string;
}

export interface OrderDelivery {
  id: number;
  delivery_type: OrderEnumRef;
  address: string | null;
  comment: string | null;
}

export interface OrderFile {
  id: number;
  disk_path: string;
  file_name: string;
  original_name: string;
  created_at: string;
}

export interface OrderItemProcessing {
  id: number;
  processing_id: number;
}

export interface OrderItem {
  id: number;
  source_item_id: number | null;
  print_id: number;
  print?: OrderEntitySummary;
  product_id: number;
  material_id: number;
  material?: OrderEntitySummary;
  option_id: number;
  option?: OrderEntitySummary;
  dpi_type: OrderEnumRef;
  variant_type: OrderEnumRef;
  width: string;
  height: string;
  quantity: number;
  performer_id: number | null;
  note: string | null;
  printed: boolean;
  ready: boolean;
  price: string;
  processings: OrderItemProcessing[];
}

export interface OrderItemMilling {
  id: number;
  source_item_id: number | null;
  print_id: number;
  print?: OrderEntitySummary;
  material: string;
  performer_id: number | null;
  note: string | null;
  printed: boolean;
  ready: boolean;
  price: string;
}

export interface OrderPayment {
  id: number;
  client_id: number;
  operation_type: OrderEnumRef;
  payment_type: OrderEnumRef;
  reason: string | null;
  note: string | null;
  confirmation: boolean;
  created_at: string;
}

export interface OrderSection {
  id: number;
  section_type: OrderEnumRef;
}

export interface OrderService {
  id: number;
  service_type: OrderEnumRef;
  price: string;
  note: string | null;
}

export interface OrderNotification {
  id: number;
  notification_type: OrderEnumRef;
  created_at: string;
}

export interface Order {
  id: number;
  creator_id: number;
  manager_id: number | null;
  designer_id: number | null;
  client_id: number;
  client: OrderEntitySummary;
  manager: OrderEntitySummary | null;
  designer: OrderEntitySummary | null;
  status_type: OrderEnumRef;
  storage_type: OrderEnumRef;
  general_note: string | null;
  extension: string | null;
  created_at: string;
  accepted_at: string;
  deadline_at: string;
  delivery: OrderDelivery | null;
  files: OrderFile[];
  items: OrderItem[];
  millings: OrderItemMilling[];
  payments: OrderPayment[];
  sections: OrderSection[];
  services: OrderService[];
  notifications: OrderNotification[];
  price: string;
}

export interface OrderDeliveryPayload {
  id?: number;
  deliveryType: number;
  address?: string | null;
  comment?: string | null;
}

export interface OrderItemProcessingPayload {
  id?: number;
  processingId: number;
}

export interface OrderItemPayload {
  id?: number;
  sourceItemId?: number | null;
  printId: number;
  productId: number;
  materialId: number;
  optionId: number;
  dpiType: number;
  variantType: number;
  width: string;
  height: string;
  quantity: number;
  price: string;
  performerId?: number | null;
  note?: string | null;
  printed?: boolean;
  ready?: boolean;
  processings?: OrderItemProcessingPayload[];
}

export interface OrderItemMillingPayload {
  id?: number;
  sourceItemId?: number | null;
  printId: number;
  material: string;
  price: string;
  performerId?: number | null;
  note?: string | null;
  printed?: boolean;
  ready?: boolean;
}

export interface OrderPaymentPayload {
  id?: number;
  clientId: number;
  operationType: number;
  paymentType: number;
  reason?: string | null;
  note?: string | null;
  confirmation?: boolean;
}

export interface OrderSectionPayload {
  id?: number;
  sectionType: number;
}

export interface OrderServicePayload {
  id?: number;
  serviceType: number;
  price: string;
  note?: string | null;
}

export interface CreateUpdateOrderBody {
  clientId: number;
  managerId?: number | null;
  designerId?: number | null;
  statusType: number;
  storageType: number;
  acceptedAt: string;
  deadlineAt: string;
  generalNote?: string | null;
  extension?: string | null;
  delivery?: OrderDeliveryPayload | null;
  items?: OrderItemPayload[];
  millings?: OrderItemMillingPayload[];
  payments?: OrderPaymentPayload[];
  sections?: OrderSectionPayload[];
  services?: OrderServicePayload[];
  files?: File[];
  fileOriginalNames?: string[];
}

export type CreateOrderBody = CreateUpdateOrderBody;

export interface UpdateOrderBody extends CreateUpdateOrderBody {
  keepFileIds?: number[];
}

export interface GetOrdersParams {
  page?: number;
  perPage?: number;
  search?: string;
  dateFrom?: string;
  dateTo?: string;
  printIds?: number[];
  materialId?: number;
  optionId?: number;
  docs?: number;
  managerId?: number;
  designerId?: number;
  statusTypes?: number[];
  storageType?: number;
  serviceType?: number;
  archived?: boolean;
  deleted?: boolean;
}

export type PaginatedOrdersResponse = PaginatedResponse<Order>;
