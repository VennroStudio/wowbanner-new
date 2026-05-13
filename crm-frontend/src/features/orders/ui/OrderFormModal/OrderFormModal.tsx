import { useEffect, useMemo, useState } from 'react';
import { useFieldArray, useForm, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Download, Trash2, X } from 'lucide-react';
import { orderApi, type OrderFile, useCreateOrderCommand, useDeleteOrderFileCommand, useOrderDeliveryTypesQuery, useOrderQuery, useOrderSectionTypesQuery, useOrderServiceTypesQuery, useOrderStatusTypesQuery, useOrderStorageTypesQuery, useUpdateOrderCommand } from '@/entities/order';
import { useMaterialDpiTypesQuery, useMaterialVariantTypesQuery } from '@/entities/material';
import { usePrintingSelectQuery } from '@/entities/printing';
import { useSessionStore } from '@/entities/session/model/useSessionStore';
import { useUserSelectQuery } from '@/entities/user';
import { useModalFormState } from '@/shared/lib/useModalFormState';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { fieldInputClass, fieldSelectClass, fieldTextareaClass, FormErrorBanner, ModalDialog } from '@/shared/ui';
import { buildCreateOrderBody, buildUpdateOrderBody, mapOrderToFormValues } from './lib/orderFormMappers';
import { getOrderFormDefaultValues, orderFormSchema, type OrderFormValues } from './lib/orderFormSchema';
import { OrderClientSelectModal } from './ui/OrderClientSelectModal';
import { OrderPrintItemsEditor } from './ui/OrderPrintItemsEditor';

interface OrderFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  orderId?: number;
  onClose: () => void;
  onSuccess?: (mode: 'create' | 'edit') => void;
}

const sectionChipClass = (selected: boolean) =>
  `inline-flex h-9 min-w-9 items-center justify-center rounded-md border text-xs font-semibold transition-colors cursor-pointer ${
    selected
      ? 'border-blue-600 bg-blue-600 text-white'
      : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
  }`;

const formatUserName = (firstName?: string, lastName?: string) =>
  [firstName, lastName].filter(Boolean).join(' ').trim() || '—';

const getExtendedDeadlineLabel = (deadlineAt: string, extension: string) => {
  if (!deadlineAt) return '—';

  const date = new Date(deadlineAt);
  if (Number.isNaN(date.getTime())) return '—';

  const extraDays = Number(extension || 0);
  if (!Number.isNaN(extraDays) && extraDays > 0) {
    date.setDate(date.getDate() + extraDays);
  }

  return new Intl.DateTimeFormat('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
};

export const OrderFormModal = ({
  open,
  mode,
  orderId,
  onClose,
  onSuccess,
}: OrderFormModalProps) => {
  const createMutation = useCreateOrderCommand();
  const updateMutation = useUpdateOrderCommand();
  const deleteOrderFileMutation = useDeleteOrderFileCommand();
  const { data: orderResponse, isLoading: isLoadingOrder } = useOrderQuery(orderId ?? 0, {
    enabled: open && mode === 'edit' && orderId != null && orderId > 0,
  });
  const currentUser = useSessionStore((state) => state.user);
  const [isClientModalOpen, setIsClientModalOpen] = useState(false);
  const [selectedClientOverride, setSelectedClientOverride] = useState<{
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    docs: string | null;
  } | null>(null);
  const [files, setFiles] = useState<File[]>([]);
  const [removedFileIds, setRemovedFileIds] = useState<number[]>([]);
  const [downloadingFileId, setDownloadingFileId] = useState<number | null>(null);

  const { data: userOptions = [], isLoading: isLoadingUsers } = useUserSelectQuery(undefined, { enabled: open });
  const { data: statusOptions = [], isLoading: isLoadingStatuses } = useOrderStatusTypesQuery();
  const { data: storageOptions = [], isLoading: isLoadingStorages } = useOrderStorageTypesQuery();
  const { data: sectionOptions = [], isLoading: isLoadingSections } = useOrderSectionTypesQuery();
  const { data: deliveryOptions = [], isLoading: isLoadingDeliveryTypes } = useOrderDeliveryTypesQuery();
  const { data: serviceOptions = [], isLoading: isLoadingServiceTypes } = useOrderServiceTypesQuery();
  const { data: printingOptions = [], isLoading: isLoadingPrintTypes } = usePrintingSelectQuery({ enabled: open });
  const { data: dpiOptions = [], isLoading: isLoadingDpiTypes } = useMaterialDpiTypesQuery({ enabled: open });
  const { data: variantOptions = [], isLoading: isLoadingVariantTypes } = useMaterialVariantTypesQuery({ enabled: open });

  const {
    register,
    control,
    handleSubmit,
    reset,
    setValue,
    formState: { errors },
  } = useForm<OrderFormValues>({
    resolver: zodResolver(orderFormSchema),
    defaultValues: getOrderFormDefaultValues(),
  });

  const { fields: serviceFields, append: appendService, remove: removeService } = useFieldArray({
    control,
    name: 'services',
    keyName: 'fieldId',
  });

  const { fields: itemFields, append: appendItem, remove: removeItem } = useFieldArray({
    control,
    name: 'items',
    keyName: 'fieldId',
  });

  const hasDelivery = useWatch({ control, name: 'hasDelivery' });
  const deadlineAt = useWatch({ control, name: 'deadlineAt' });
  const extension = useWatch({ control, name: 'extension' });
  const selectedSections = useWatch({ control, name: 'sections' });
  const selectedStorageType = useWatch({ control, name: 'storageType' });

  const isDictsLoading =
    isLoadingUsers ||
    isLoadingStatuses ||
    isLoadingStorages ||
    isLoadingSections ||
    isLoadingDeliveryTypes ||
    isLoadingServiceTypes ||
    isLoadingPrintTypes ||
    isLoadingDpiTypes ||
    isLoadingVariantTypes;

  const managerOptions = userOptions;
  const designerOptions = userOptions;
  const performerOptions = userOptions;

  const extendedDeadlineLabel = useMemo(
    () => getExtendedDeadlineLabel(deadlineAt, extension),
    [deadlineAt, extension],
  );

  const { submitError, setSubmitError, handleClose } = useModalFormState({
    onClose,
    onReset: () => {
      reset(getOrderFormDefaultValues());
      setSelectedClientOverride(null);
      setFiles([]);
      setRemovedFileIds([]);
      setIsClientModalOpen(false);
    },
  });

  useEffect(() => {
    if (!open || mode !== 'create') return;

    reset(getOrderFormDefaultValues());
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    if (isDictsLoading) return;

    const order = orderResponse?.data;
    if (!order) return;

    reset(mapOrderToFormValues(order));
  }, [isDictsLoading, mode, open, orderResponse, reset]);

  const toggleSection = (sectionId: string) => {
    const current = selectedSections ?? [];
    const next = current.includes(sectionId)
      ? current.filter((value) => value !== sectionId)
      : [...current, sectionId];

    setValue('sections', next, { shouldDirty: true, shouldValidate: true });
  };

  const onSubmit = async (values: OrderFormValues) => {
    setSubmitError(null);

    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateOrderBody(values, files));
      } else if (orderId != null) {
        await updateMutation.mutateAsync({
          id: orderId,
          body: buildUpdateOrderBody(values, files, keepFileIds),
        });
      }
      onSuccess?.(mode);
      handleClose();
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const handleFilesChange = (nextFiles: FileList | null) => {
    if (!nextFiles) return;
    setFiles(Array.from(nextFiles));
  };

  const handleRemoveFile = (index: number) => {
    setFiles((current) => current.filter((_, fileIndex) => fileIndex !== index));
  };

  const handleRemoveExistingFile = (fileId: number) => {
    setRemovedFileIds((current) => (
      current.includes(fileId) ? current : [...current, fileId]
    ));
  };

  const handleDownloadExistingFile = async (file: OrderFile) => {
    setSubmitError(null);
    setDownloadingFileId(file.id);

    try {
      const blob = await orderApi.downloadOrderFile(file.id);
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = file.original_name || file.file_name;
      document.body.append(link);
      link.click();
      link.remove();
      URL.revokeObjectURL(url);
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    } finally {
      setDownloadingFileId(null);
    }
  };

  const handleDeleteExistingFile = async (fileId: number) => {
    if (orderId == null) return;
    setSubmitError(null);

    try {
      await deleteOrderFileMutation.mutateAsync({ fileId, orderId });
      handleRemoveExistingFile(fileId);
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const handleSelectClient = (client: {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    docs: string | null;
  }) => {
    setSelectedClientOverride(client);
    setValue('clientId', String(client.id), { shouldDirty: true, shouldValidate: true });
    setIsClientModalOpen(false);
  };

  const existingFiles = orderResponse?.data?.files ?? [];
  const keepFileIds = existingFiles
    .filter((file) => !removedFileIds.includes(file.id))
    .map((file) => file.id);
  const visibleExistingFiles = existingFiles.filter((file) => keepFileIds.includes(file.id));
  const selectedClient = selectedClientOverride ?? (
    mode === 'edit' && orderResponse?.data
      ? {
          id: orderResponse.data.client_id,
          name: orderResponse.data.client?.name ?? `Клиент #${orderResponse.data.client_id}`,
          email: orderResponse.data.client?.email ?? null,
          phone: orderResponse.data.client?.phones?.[0]?.phone ?? null,
          docs: orderResponse.data.client?.docs?.label ?? null,
        }
      : null
  );
  const isPending = createMutation.isPending || updateMutation.isPending;
  const showLoader = isDictsLoading || (mode === 'edit' && isLoadingOrder);
  const title = mode === 'create' ? 'Новый заказ' : 'Редактирование заказа';
  const submitLabel = mode === 'create' ? 'Создать' : 'Сохранить';
  const pendingLabel = mode === 'create' ? 'Создание…' : 'Сохранение…';

  return (
    <>
      <ModalDialog
        open={open}
        title={title}
        titleId="order-form-title"
        onClose={handleClose}
        size="7xl"
      >
        {showLoader ? (
          <div className="p-12 text-center text-sm text-slate-500">Загрузка…</div>
        ) : (
          <form onSubmit={handleSubmit(onSubmit)} className="flex min-h-0 flex-1 flex-col overflow-hidden">
            <div className="overflow-y-auto px-5 py-4 space-y-5">
              <FormErrorBanner message={submitError} />

              <div className="rounded-2xl border border-slate-200 bg-white p-4">
                <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                  <div className="rounded-xl border border-slate-200 p-4 space-y-3 min-h-[150px]">
                      <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Ответственные</p>

                      <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div className="rounded-lg border border-slate-200 px-3 py-2">
                          <div className="text-xs font-medium text-slate-500">Создал(а)</div>
                          <div className="mt-1 text-sm font-medium text-slate-800">
                            {formatUserName(currentUser?.first_name, currentUser?.last_name)}
                          </div>
                        </div>

                        <label className="block">
                          <span className="mb-1 block text-xs font-medium text-slate-600">Менеджер</span>
                          <select className={fieldSelectClass} {...register('managerId')}>
                            <option value="">Не выбран</option>
                            {managerOptions.map((option) => (
                              <option key={option.id} value={option.id}>
                                {option.name}
                              </option>
                            ))}
                          </select>
                        </label>

                        <label className="block">
                          <span className="mb-1 block text-xs font-medium text-slate-600">Дизайнер</span>
                          <select className={fieldSelectClass} {...register('designerId')}>
                            <option value="">Не выбран</option>
                            {designerOptions.map((option) => (
                              <option key={option.id} value={option.id}>
                                {option.name}
                              </option>
                            ))}
                          </select>
                        </label>
                      </div>
                  </div>

                  <div className="rounded-xl border border-slate-200 p-4 space-y-3 min-h-[150px]">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Сроки</p>
                    <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                      <label className="block">
                        <span className="mb-1 block text-xs font-medium text-slate-600">Дата постановки *</span>
                        <input type="datetime-local" className={fieldInputClass} {...register('acceptedAt')} />
                        {errors.acceptedAt ? <p className="mt-1 text-xs text-red-600">{errors.acceptedAt.message}</p> : null}
                      </label>

                      <label className="block">
                        <span className="mb-1 block text-xs font-medium text-slate-600">Дата сдачи *</span>
                        <input type="datetime-local" className={fieldInputClass} {...register('deadlineAt')} />
                        {errors.deadlineAt ? <p className="mt-1 text-xs text-red-600">{errors.deadlineAt.message}</p> : null}
                      </label>

                      <label className="block">
                        <span className="mb-1 block text-xs font-medium text-slate-600">Расширение (дней)</span>
                        <input
                          type="number"
                          min="0"
                          step="1"
                          className={fieldInputClass}
                          placeholder="0"
                          {...register('extension')}
                        />
                      </label>
                    </div>

                    <div className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                      Итоговый срок с расширением: <span className="font-medium text-slate-900">{extendedDeadlineLabel}</span>
                    </div>
                  </div>

                  <div className="rounded-xl border border-slate-200 p-4 space-y-3 min-h-[190px]">
                      <div className="flex items-center justify-between gap-3">
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Заказчик</p>
                        <button
                          type="button"
                          onClick={() => setIsClientModalOpen(true)}
                          className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50 cursor-pointer"
                        >
                          {selectedClient ? 'Сменить клиента' : 'Добавить клиента'}
                        </button>
                      </div>

                      <input type="hidden" {...register('clientId')} />
                      {errors.clientId ? <p className="text-xs text-red-600">{errors.clientId.message}</p> : null}

                      {selectedClient ? (
                        <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                          <div className="rounded-lg border border-slate-200 px-3 py-2">
                            <div className="text-xs font-medium text-slate-500">Клиент № {selectedClient.id}</div>
                            <div className="mt-1 text-sm font-medium text-slate-800">{selectedClient.name}</div>
                          </div>
                          <div className="rounded-lg border border-slate-200 px-3 py-2">
                            <div className="text-xs font-medium text-slate-500">Контакты</div>
                            <div className="mt-1 text-sm text-slate-700">{selectedClient.phone || 'Телефон не указан'}</div>
                            <div className="text-sm text-slate-700">{selectedClient.email || 'Email не указан'}</div>
                            <div className="text-sm text-slate-500">Документы: {selectedClient.docs || '—'}</div>
                          </div>
                        </div>
                      ) : (
                        <div className="flex min-h-[112px] items-center rounded-lg border border-dashed border-slate-300 px-4 py-5 text-sm text-slate-500">
                          Клиент пока не выбран.
                        </div>
                      )}
                  </div>

                  <div className="rounded-xl border border-slate-200 p-4 space-y-3 min-h-[190px]">
                      <label className="block h-full">
                        <span className="mb-1 block text-xs font-medium text-slate-600">Общее примечание к заказу</span>
                        <textarea rows={6} className={`${fieldTextareaClass} min-h-[128px]`} {...register('generalNote')} />
                      </label>
                  </div>
                </div>
              </div>

              <OrderPrintItemsEditor
                control={control}
                register={register}
                setValue={setValue}
                errors={errors}
                fields={itemFields}
                append={appendItem}
                remove={removeItem}
                printOptions={printingOptions}
                performerOptions={performerOptions}
                dpiOptions={dpiOptions}
                variantOptions={variantOptions}
                order={orderResponse?.data}
                disabled={isPending}
              />

              <div className="rounded-2xl border border-slate-200 bg-white p-4 space-y-4">
                <div className="grid grid-cols-1 gap-4 xl:grid-cols-3">
                  <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Склад и секции</p>

                    <label className="block">
                      <span className="mb-1 block text-xs font-medium text-slate-600">Склад *</span>
                      <select className={fieldSelectClass} {...register('storageType')}>
                        <option value="">Выберите склад</option>
                        {storageOptions.map((option) => (
                          <option key={option.id} value={option.id}>
                            {option.label}
                          </option>
                        ))}
                      </select>
                      {errors.storageType ? <p className="mt-1 text-xs text-red-600">{errors.storageType.message}</p> : null}
                    </label>

                    <div>
                      <div className="mb-2 text-xs font-medium text-slate-600">
                        Секции {selectedStorageType ? '' : '(сначала выберите склад)'}
                      </div>
                      <div className="flex flex-wrap justify-center gap-2">
                        {sectionOptions.map((section) => {
                          const selected = (selectedSections ?? []).includes(String(section.id));
                          return (
                            <button
                              key={section.id}
                              type="button"
                              disabled={!selectedStorageType}
                              onClick={() => toggleSection(String(section.id))}
                              className={`${sectionChipClass(selected)} disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-300`}
                              title={section.label}
                            >
                              {section.label.replace(/\D+/g, '') || section.id}
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  </div>

                  <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Файлы</p>
                    <input
                      type="file"
                      multiple
                      onChange={(e) => handleFilesChange(e.target.files)}
                      className="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100"
                    />

                    {files.length > 0 ? (
                      <div className="space-y-2">
                        {files.map((file, index) => (
                          <div
                            key={`${file.name}-${index}`}
                            className="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2"
                          >
                            <div className="min-w-0">
                              <p className="truncate text-sm font-medium text-slate-700">{file.name}</p>
                              <p className="text-xs text-slate-500">{Math.round(file.size / 1024)} КБ</p>
                            </div>

                            <button
                              type="button"
                              onClick={() => handleRemoveFile(index)}
                              title="Убрать из списка"
                              aria-label={`Убрать файл ${file.name} из списка`}
                              className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50 hover:text-slate-700 cursor-pointer"
                            >
                              <X size={15} />
                            </button>
                          </div>
                        ))}
                      </div>
                    ) : (
                      <p className="text-sm text-slate-500">Новые файлы пока не добавлены.</p>
                    )}

                    {mode === 'edit' && visibleExistingFiles.length > 0 ? (
                      <div className="space-y-2">
                        <p className="text-xs font-medium uppercase tracking-wide text-slate-400">Текущие файлы</p>
                        {visibleExistingFiles.map((file) => (
                          <div
                            key={file.id}
                            className="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2"
                          >
                            <div className="min-w-0 truncate text-sm font-medium text-slate-700">
                              {file.original_name || file.file_name}
                            </div>

                            <div className="flex shrink-0 items-center gap-2">
                              <button
                                type="button"
                                onClick={() => handleDownloadExistingFile(file)}
                                disabled={downloadingFileId === file.id}
                                title="Скачать"
                                aria-label={`Скачать файл ${file.original_name || file.file_name}`}
                                className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-blue-700 transition-colors hover:bg-blue-100 disabled:cursor-wait disabled:opacity-60 cursor-pointer"
                              >
                                <Download size={15} />
                              </button>

                              <button
                                type="button"
                                onClick={() => handleDeleteExistingFile(file.id)}
                                disabled={deleteOrderFileMutation.isPending}
                                title="Удалить"
                                aria-label={`Удалить файл ${file.original_name || file.file_name}`}
                                className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 bg-red-50 text-red-600 transition-colors hover:bg-red-100 disabled:cursor-wait disabled:opacity-60 cursor-pointer"
                              >
                                <Trash2 size={15} />
                              </button>
                            </div>
                          </div>
                        ))}
                      </div>
                    ) : null}
                  </div>

                  <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Доставка</p>
                    <label className="flex items-center gap-2 text-sm font-medium text-slate-700">
                      <input
                        type="checkbox"
                        className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        {...register('hasDelivery')}
                      />
                      Есть доставка
                    </label>

                    {hasDelivery ? (
                      <div className="space-y-3">
                        <input type="hidden" {...register('deliveryId')} />

                        <label className="block">
                          <span className="mb-1 block text-xs font-medium text-slate-600">Тип доставки *</span>
                          <select className={fieldSelectClass} {...register('deliveryType')}>
                            <option value="">Выберите тип</option>
                            {deliveryOptions.map((option) => (
                              <option key={option.id} value={option.id}>
                                {option.label}
                              </option>
                            ))}
                          </select>
                          {errors.deliveryType ? <p className="mt-1 text-xs text-red-600">{errors.deliveryType.message}</p> : null}
                        </label>

                        <label className="block">
                          <span className="mb-1 block text-xs font-medium text-slate-600">Адрес</span>
                          <input type="text" className={fieldInputClass} {...register('deliveryAddress')} />
                        </label>

                        <label className="block">
                          <span className="mb-1 block text-xs font-medium text-slate-600">Комментарий</span>
                          <textarea rows={4} className={fieldTextareaClass} {...register('deliveryComment')} />
                        </label>
                      </div>
                    ) : (
                      <p className="text-sm text-slate-500">Доставка пока не требуется.</p>
                    )}
                  </div>
                </div>

                <div className="flex items-center justify-between gap-3">
                  <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Услуги</p>
                  <button
                    type="button"
                    onClick={() => appendService({ id: '', serviceType: '', price: '', note: '' })}
                    className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50 cursor-pointer"
                  >
                    Добавить услугу
                  </button>
                </div>

                <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                  {serviceFields.length === 0 ? (
                    <p className="text-sm text-slate-500">Пока без услуг.</p>
                  ) : (
                    <div className="grid grid-cols-1 gap-3 xl:grid-cols-2">
                      {serviceFields.map((field, index) => (
                        <div key={field.fieldId} className="rounded-xl border border-slate-200 p-3">
                          <input type="hidden" {...register(`services.${index}.id`)} />

                          <div className="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_180px]">
                            <label className="block">
                              <span className="mb-1 block text-xs font-medium text-slate-600">Услуга</span>
                              <select className={fieldSelectClass} {...register(`services.${index}.serviceType`)}>
                                <option value="">Выберите услугу</option>
                                {serviceOptions.map((option) => (
                                  <option key={option.id} value={option.id}>
                                    {option.label}
                                  </option>
                                ))}
                              </select>
                              {errors.services?.[index]?.serviceType ? (
                                <p className="mt-1 text-xs text-red-600">{errors.services[index]?.serviceType?.message}</p>
                              ) : null}
                            </label>

                            <label className="block">
                              <span className="mb-1 block text-xs font-medium text-slate-600">Цена</span>
                              <input className={fieldInputClass} {...register(`services.${index}.price`)} />
                              {errors.services?.[index]?.price ? (
                                <p className="mt-1 text-xs text-red-600">{errors.services[index]?.price?.message}</p>
                              ) : null}
                            </label>
                          </div>

                          <label className="mt-3 block">
                            <span className="mb-1 block text-xs font-medium text-slate-600">Примечание</span>
                            <input className={fieldInputClass} {...register(`services.${index}.note`)} />
                          </label>

                          <button
                            type="button"
                            onClick={() => removeService(index)}
                            className="mt-3 w-full rounded-lg border border-rose-200 px-3 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 cursor-pointer"
                          >
                            Удалить
                          </button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>
            </div>

            <div className="flex items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/80 px-5 py-4 shrink-0">
              <div className="flex min-w-0 items-center gap-3">
                <span className="text-sm font-medium text-slate-600">Статус:</span>
                <select
                  className={`${fieldSelectClass} h-10 min-w-[260px] max-w-[320px]`}
                  {...register('statusType')}
                >
                  <option value="">Выберите статус</option>
                  {statusOptions.map((option) => (
                    <option key={option.id} value={option.id}>
                      {option.label}
                    </option>
                  ))}
                </select>
              </div>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={handleClose}
                  className="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                >
                  Отмена
                </button>
                <button
                  type="submit"
                  disabled={isPending}
                  className="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50"
                >
                  {isPending ? pendingLabel : submitLabel}
                </button>
              </div>
            </div>
          </form>
        )}
      </ModalDialog>

      <OrderClientSelectModal
        open={isClientModalOpen}
        selectedClientId={selectedClient?.id}
        onClose={() => setIsClientModalOpen(false)}
        onSelect={handleSelectClient}
      />
    </>
  );
};
