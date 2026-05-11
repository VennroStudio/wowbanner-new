import { useMemo, useState } from 'react';
import { useFieldArray, useForm, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
  type Client,
  useClientsQuery,
} from '@/entities/client';
import {
  useCreateOrderCommand,
  useOrderDeliveryTypesQuery,
  useOrderServiceTypesQuery,
  useOrderStatusTypesQuery,
  useOrderStorageTypesQuery,
} from '@/entities/order';
import { useUserSelectQuery } from '@/entities/user';
import { useModalFormState } from '@/shared/lib/useModalFormState';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import {
  fieldInputClass,
  fieldSelectClass,
  fieldTextareaClass,
  FormErrorBanner,
  FormModalFooter,
  ModalDialog,
  SearchField,
} from '@/shared/ui';
import { buildCreateOrderBody } from './lib/orderFormMappers';
import { getOrderFormDefaultValues, orderFormSchema, type OrderFormValues } from './lib/orderFormSchema';

interface OrderFormModalProps {
  open: boolean;
  onClose: () => void;
  onSuccess?: () => void;
}

interface ClientSelectOption {
  id: number;
  name: string;
}

const getClientDisplayName = (client: Client) => {
  const old = client.old_full_name?.trim();
  if (old) return old;

  return [client.last_name, client.first_name, client.middle_name].filter(Boolean).join(' ');
};

export const OrderFormModal = ({
  open,
  onClose,
  onSuccess,
}: OrderFormModalProps) => {
  const createMutation = useCreateOrderCommand();
  const [clientSearch, setClientSearch] = useState('');
  const [selectedClient, setSelectedClient] = useState<ClientSelectOption | null>(null);
  const [files, setFiles] = useState<File[]>([]);

  const {
    data: clientsResponse,
    isLoading: isLoadingClients,
  } = useClientsQuery({
    page: 1,
    perPage: 100,
    search: clientSearch,
  });
  const { data: managerOptions = [], isLoading: isLoadingManagers } = useUserSelectQuery(undefined, { enabled: open });
  const { data: designerOptions = [], isLoading: isLoadingDesigners } = useUserSelectQuery(undefined, { enabled: open });
  const { data: statusOptions = [], isLoading: isLoadingStatuses } = useOrderStatusTypesQuery();
  const { data: storageOptions = [], isLoading: isLoadingStorages } = useOrderStorageTypesQuery();
  const { data: deliveryOptions = [], isLoading: isLoadingDeliveryTypes } = useOrderDeliveryTypesQuery();
  const { data: serviceOptions = [], isLoading: isLoadingServiceTypes } = useOrderServiceTypesQuery();

  const clientOptions = useMemo<ClientSelectOption[]>(() => {
    const base = (clientsResponse?.data?.items ?? []).map((item) => ({
      id: item.id,
      name: getClientDisplayName(item),
    }));

    if (!selectedClient) return base;
    if (base.some((item) => item.id === selectedClient.id)) return base;

    return [selectedClient, ...base];
  }, [clientsResponse?.data?.items, selectedClient]);

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

  const watchedClientId = useWatch({ control, name: 'clientId' });
  const hasDelivery = useWatch({ control, name: 'hasDelivery' });
  const isDictsLoading =
    isLoadingClients ||
    isLoadingManagers ||
    isLoadingDesigners ||
    isLoadingStatuses ||
    isLoadingStorages ||
    isLoadingDeliveryTypes ||
    isLoadingServiceTypes;

  const { submitError, setSubmitError, handleClose } = useModalFormState({
    onClose,
    onReset: () => {
      reset(getOrderFormDefaultValues());
      setClientSearch('');
      setSelectedClient(null);
      setFiles([]);
    },
  });

  const onSubmit = async (values: OrderFormValues) => {
    setSubmitError(null);

    try {
      await createMutation.mutateAsync(buildCreateOrderBody(values, files));
      onSuccess?.();
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

  const isPending = createMutation.isPending;

  return (
    <ModalDialog
      open={open}
      title="Новый заказ"
      titleId="order-form-title"
      onClose={handleClose}
      size="4xl"
    >
      {isDictsLoading ? (
        <div className="p-12 text-center text-sm text-slate-500">Загрузка…</div>
      ) : (
        <form onSubmit={handleSubmit(onSubmit)} className="flex min-h-0 flex-1 flex-col overflow-hidden">
          <div className="overflow-y-auto px-5 py-4 space-y-5">
            <FormErrorBanner message={submitError} />

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
              <div className="space-y-2">
                <label className="block">
                  <span className="mb-1 block text-xs font-medium text-slate-600">Поиск заказчика</span>
                  <SearchField
                    value={clientSearch}
                    onChange={setClientSearch}
                    placeholder="Поиск клиента…"
                  />
                </label>

                <label className="block">
                  <span className="mb-1 block text-xs font-medium text-slate-600">Заказчик *</span>
                  <select
                    value={watchedClientId}
                    onChange={(e) => {
                      const nextValue = e.target.value;
                      const match = clientOptions.find((option) => String(option.id) === nextValue) ?? null;
                      setSelectedClient(match);
                      setValue('clientId', e.target.value, { shouldDirty: true, shouldValidate: true });
                    }}
                    className={fieldSelectClass}
                  >
                    <option value="">Выберите заказчика</option>
                    {clientOptions.map((option) => (
                      <option key={option.id} value={option.id}>
                        {option.name}
                      </option>
                    ))}
                  </select>
                  {errors.clientId && <p className="mt-1 text-xs text-red-600">{errors.clientId.message}</p>}
                </label>
              </div>

              <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Ответственные</p>
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
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
            </div>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
              <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Статус и склад</p>
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <label className="block">
                    <span className="mb-1 block text-xs font-medium text-slate-600">Статус *</span>
                    <select className={fieldSelectClass} {...register('statusType')}>
                      <option value="">Выберите статус</option>
                      {statusOptions.map((option) => (
                        <option key={option.id} value={option.id}>
                          {option.label}
                        </option>
                      ))}
                    </select>
                    {errors.statusType && <p className="mt-1 text-xs text-red-600">{errors.statusType.message}</p>}
                  </label>

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
                    {errors.storageType && <p className="mt-1 text-xs text-red-600">{errors.storageType.message}</p>}
                  </label>
                </div>
              </div>

              <div className="rounded-xl border border-slate-200 p-4 space-y-3">
                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Сроки</p>
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <label className="block">
                    <span className="mb-1 block text-xs font-medium text-slate-600">Дата постановки *</span>
                    <input type="datetime-local" className={fieldInputClass} {...register('acceptedAt')} />
                    {errors.acceptedAt && <p className="mt-1 text-xs text-red-600">{errors.acceptedAt.message}</p>}
                  </label>

                  <label className="block">
                    <span className="mb-1 block text-xs font-medium text-slate-600">Дата сдачи *</span>
                    <input type="datetime-local" className={fieldInputClass} {...register('deadlineAt')} />
                    {errors.deadlineAt && <p className="mt-1 text-xs text-red-600">{errors.deadlineAt.message}</p>}
                  </label>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
              <label className="block">
                <span className="mb-1 block text-xs font-medium text-slate-600">Расширение</span>
                <input type="text" className={fieldInputClass} placeholder="cdr, pdf, ai…" {...register('extension')} />
              </label>

              <label className="block">
                <span className="mb-1 block text-xs font-medium text-slate-600">Описание</span>
                <textarea rows={3} className={fieldTextareaClass} {...register('generalNote')} />
              </label>
            </div>

            <div className="rounded-xl border border-slate-200 p-4 space-y-3">
              <label className="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input
                  type="checkbox"
                  className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  {...register('hasDelivery')}
                />
                Есть доставка
              </label>

              {hasDelivery ? (
                <div className="grid grid-cols-1 gap-3 lg:grid-cols-3">
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
                    {errors.deliveryType && <p className="mt-1 text-xs text-red-600">{errors.deliveryType.message}</p>}
                  </label>

                  <label className="block lg:col-span-2">
                    <span className="mb-1 block text-xs font-medium text-slate-600">Адрес</span>
                    <input type="text" className={fieldInputClass} {...register('deliveryAddress')} />
                  </label>

                  <label className="block lg:col-span-3">
                    <span className="mb-1 block text-xs font-medium text-slate-600">Комментарий к доставке</span>
                    <textarea rows={2} className={fieldTextareaClass} {...register('deliveryComment')} />
                  </label>
                </div>
              ) : null}
            </div>

            <div className="rounded-xl border border-slate-200 p-4 space-y-3">
              <div className="flex items-center justify-between gap-3">
                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Услуги</p>
                <button
                  type="button"
                  onClick={() => appendService({ serviceType: '', price: '', note: '' })}
                  className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50 cursor-pointer"
                >
                  Добавить услугу
                </button>
              </div>

              {serviceFields.length === 0 ? (
                <p className="text-sm text-slate-500">Пока без услуг.</p>
              ) : (
                <div className="space-y-3">
                  {serviceFields.map((field, index) => (
                    <div key={field.fieldId} className="rounded-xl border border-slate-200 p-3">
                      <div className="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_180px_auto]">
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
                          {errors.services?.[index]?.serviceType && (
                            <p className="mt-1 text-xs text-red-600">{errors.services[index]?.serviceType?.message}</p>
                          )}
                        </label>

                        <label className="block">
                          <span className="mb-1 block text-xs font-medium text-slate-600">Цена</span>
                          <input className={fieldInputClass} {...register(`services.${index}.price`)} />
                          {errors.services?.[index]?.price && (
                            <p className="mt-1 text-xs text-red-600">{errors.services[index]?.price?.message}</p>
                          )}
                        </label>

                        <div className="flex items-end">
                          <button
                            type="button"
                            onClick={() => removeService(index)}
                            className="w-full rounded-lg border border-rose-200 px-3 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 cursor-pointer"
                          >
                            Удалить
                          </button>
                        </div>
                      </div>

                      <label className="mt-3 block">
                        <span className="mb-1 block text-xs font-medium text-slate-600">Примечание</span>
                        <input className={fieldInputClass} {...register(`services.${index}.note`)} />
                      </label>
                    </div>
                  ))}
                </div>
              )}
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
                        className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 cursor-pointer"
                      >
                        Убрать
                      </button>
                    </div>
                  ))}
                </div>
              ) : null}
            </div>
          </div>

          <FormModalFooter mode="create" isPending={isPending} onClose={handleClose} />
        </form>
      )}
    </ModalDialog>
  );
};
